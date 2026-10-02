<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\SouvenirProductRequest;
use App\Models\MediaAsset;
use App\Models\SouvenirOrder;
use App\Models\SouvenirProduct;
use App\Models\SouvenirReview;
use App\Models\Vendor;
use App\Services\AuditService;
use App\Services\SouvenirCommerceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SouvenirController extends Controller
{
    public function replyReview(Request $request, SouvenirReview $review, AuditService $audit): RedirectResponse
    {
        abort_unless($review->product->vendor->user_id === $request->user()->id, 404);
        $data = $request->validate(['vendor_response' => ['required', 'string', 'min:5', 'max:2000']]);
        $review->update([...$data, 'status' => 'pending']);
        $audit->record('souvenir.review.replied', $review);

        return back();
    }

    public function moderateReview(Request $request, SouvenirReview $review, AuditService $audit): RedirectResponse
    {
        $review->update($request->validate(['status' => ['required', 'in:published,rejected']]));
        $audit->record('souvenir.review.moderated', $review);

        return back();
    }

    public function index(Request $request): Response
    {
        $vendor = Vendor::where('user_id', $request->user()->id)->firstOrFail();

        return Inertia::render('Vendor/Souvenirs', ['vendor' => $vendor, 'products' => SouvenirProduct::where('vendor_id', $vendor->id)->latest('id')->simplePaginate(20, ['*'], 'products_page'), 'orders' => SouvenirOrder::where('vendor_id', $vendor->id)->with('items', 'payment')->latest('id')->simplePaginate(20, ['*'], 'orders_page'), 'reviews' => SouvenirReview::whereHas('product', fn ($q) => $q->where('vendor_id', $vendor->id))->with('product:id,name')->latest('id')->simplePaginate(20, ['*'], 'reviews_page')]);
    }

    public function save(SouvenirProductRequest $request, ?SouvenirProduct $product = null): RedirectResponse
    {
        $vendor = Vendor::where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($vendor->status === 'verified', 403);
        abort_if($product && $product->vendor_id !== $vendor->id, 404);
        $data = $request->validated();
        if (! empty($data['image_url'])) {
            $imageId = basename($data['image_url']);
            if (! MediaAsset::whereKey($imageId)->where('user_id', $request->user()->id)->where('visibility', 'public')->where('mime_type', 'like', 'image/%')->exists()) {
                throw ValidationException::withMessages(['image_url' => 'Gunakan foto publik yang diunggah dari akun Anda.']);
            }
        }
        DB::transaction(function () use ($data, $product, $vendor): void {
            if ($product) {
                $product = SouvenirProduct::whereKey($product->id)->lockForUpdate()->firstOrFail();
                if ($data['stock'] < $product->reserved_stock) {
                    throw ValidationException::withMessages(['stock' => 'Stok tidak boleh di bawah jumlah yang sedang dipesan.']);
                }
            }
            $vendor = Vendor::whereKey($vendor->id)->lockForUpdate()->firstOrFail();
            abort_unless($vendor->status === 'verified', 403);
            if ($product) {
                $product->update([...$data, 'status' => 'pending']);
            } else {
                $product = SouvenirProduct::create([...$data, 'vendor_id' => $vendor->id, 'slug' => Str::slug($data['name']).'-'.Str::lower((string) Str::ulid()), 'status' => 'pending']);
            }
            app(AuditService::class)->record('souvenir.product.submitted', $product);
        }, 3);

        return back()->with('success', 'Produk diajukan untuk moderasi.');
    }

    public function transition(Request $request, SouvenirOrder $order, SouvenirCommerceService $service): RedirectResponse
    {
        abort_unless($order->vendor->user_id === $request->user()->id && $order->vendor->status === 'verified', 404);
        $data = $request->validate(['status' => ['required', 'in:processing,ready_for_pickup,shipped'], 'tracking_number' => ['nullable', 'string', 'max:100']]);
        $service->transition($order, $data['status'], $data['tracking_number'] ?? null);

        return back();
    }

    public function moderation(): Response
    {
        return Inertia::render('Vendor/Souvenirs', ['moderation' => true, 'products' => SouvenirProduct::with('vendor:id,name')->latest('id')->simplePaginate(20), 'orders' => null, 'reviews' => SouvenirReview::with('product:id,name')->latest('id')->simplePaginate(20, ['*'], 'reviews_page')]);
    }

    public function moderate(Request $request, SouvenirProduct $product, AuditService $audit): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:published,rejected,archived']]);
        DB::transaction(function () use ($data, $product, $audit): void {
            $product = SouvenirProduct::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $vendor = Vendor::whereKey($product->vendor_id)->lockForUpdate()->firstOrFail();
            if ($data['status'] === 'published' && $vendor->status !== 'verified') {
                throw ValidationException::withMessages(['status' => 'Vendor belum terverifikasi.']);
            }
            $product->update($data);
            $audit->record('souvenir.product.'.$data['status'], $product);
        }, 3);

        return back()->with('success', 'Status produk diperbarui.');
    }
}
