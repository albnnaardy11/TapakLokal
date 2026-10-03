<?php

namespace App\Http\Controllers;

use App\Http\Requests\SouvenirCheckoutRequest;
use App\Models\SouvenirCartItem;
use App\Models\SouvenirOrder;
use App\Models\SouvenirOrderItem;
use App\Models\SouvenirProduct;
use App\Models\SouvenirReview;
use App\Models\User;
use App\Models\Vendor;
use App\Services\SouvenirCommerceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SouvenirController extends Controller
{
    public function review(Request $request): RedirectResponse
    {
        $data = $request->validate(['item_id' => ['required', 'integer'], 'rating' => ['required', 'integer', 'between:1,5'], 'body' => ['required', 'string', 'min:10', 'max:2000']]);
        DB::transaction(function () use ($request, $data): void {
            $item = SouvenirOrderItem::findOrFail($data['item_id']);
            $order = SouvenirOrder::where('user_id', $request->user()->id)->where('status', 'completed')->lockForUpdate()->findOrFail($item->souvenir_order_id);
            if (SouvenirReview::where('souvenir_order_item_id', $item->id)->exists()) {
                throw ValidationException::withMessages(['review' => 'Produk dalam pesanan ini sudah diulas.']);
            }
            SouvenirReview::create(['souvenir_order_item_id' => $item->id, 'souvenir_product_id' => $item->souvenir_product_id, 'user_id' => $order->user_id, 'rating' => $data['rating'], 'body' => $data['body'], 'status' => 'pending']);
        });

        return back()->with('success', 'Ulasan diterima dan akan ditinjau sebelum tampil.');
    }

    public function index(Request $request): Response
    {
        foreach (['region', 'category', 'availability', 'reception'] as $field) {
            if ($request->filled($field) && ! is_array($request->input($field))) {
                $request->merge([$field => [$request->input($field)]]);
            }
        }
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'region' => ['nullable', 'array', 'max:40'], 'region.*' => ['string', 'max:100'], 'category' => ['nullable', 'array', 'max:3'], 'category.*' => ['string', 'max:100'], 'availability' => ['nullable', 'array', 'max:2'], 'availability.*' => ['in:Preorder,Ready stock'], 'reception' => ['nullable', 'array', 'max:2'], 'reception.*' => ['in:pickup,delivery'], 'sort' => ['nullable', 'in:recommended,cheapest,expensive'], 'min' => ['nullable', 'integer', 'min:0'], 'max' => ['nullable', 'integer', 'min:0'], 'tab' => ['nullable', 'in:products,stores']]);
        $query = SouvenirProduct::published()->with('vendor:id,name,city,description');
        foreach (['region', 'category', 'availability'] as $field) {
            $query->when($filters[$field] ?? null, fn ($q, $value) => $q->whereIn($field, $value));
        }
        $query->when($filters['q'] ?? null, function ($q, $value): void {
            foreach (preg_split('/\s+/', trim($value)) as $term) {
                $q->where(fn ($q) => $q->where('name', 'like', '%'.$term.'%')->orWhere('category', 'like', '%'.$term.'%')->orWhereHas('vendor', fn ($v) => $v->where('name', 'like', '%'.$term.'%')->orWhere('city', 'like', '%'.$term.'%')));
            }
        });
        if (($filters['reception'] ?? []) === ['delivery']) {
            $query->where('pickup_only', false)->whereJsonLength('delivery_rates', '>', 0);
        }
        $bps = max(800, min(1500, (int) config('platform.markup_bps')));
        $denominator = 10000 + $bps;
        $query->when(isset($filters['min']), fn ($q) => $q->where('price', '>=', (int) ceil(((int) $filters['min']) * 10000 / $denominator)));
        $query->when(isset($filters['max']), fn ($q) => $q->where('price', '<=', (int) ceil((((int) $filters['max']) + 1) * 10000 / $denominator) - 1));
        $sort = $filters['sort'] ?? 'recommended';
        $records = $query->when($sort !== 'recommended', fn ($q) => $q->orderBy('price', $sort === 'cheapest' ? 'asc' : 'desc'))->orderByDesc('id')->cursorPaginate(24)->withQueryString();
        $products = $records->getCollection()->map(fn ($product) => $this->productData($product));

        return Inertia::render('SouvenirMarketplace', [...$this->shared($request), 'view' => 'catalog', 'products' => $products, 'shops' => $products->pluck('shop')->unique('id')->values(), 'pagination' => ['previous' => $records->previousPageUrl(), 'next' => $records->nextPageUrl()], 'filters' => $filters])
            ->withViewData('seo', ['title' => 'Oleh-oleh & Produk Lokal', 'description' => 'Pesan oleh-oleh, makanan khas, dan kerajinan dari mitra lokal terverifikasi. Pilih kirim ke rumah atau ambil di tempat.', 'canonical' => route('souvenirs.index'), 'robots' => $request->query() ? 'noindex,follow' : 'index,follow']);
    }

    public function show(Request $request, string $product): Response
    {
        $item = SouvenirProduct::published()->with('vendor')->where('slug', $product)->firstOrFail();
        $products = SouvenirProduct::published()->with('vendor')->where('vendor_id', $item->vendor_id)->whereKeyNot($item->id)->limit(12)->get()->prepend($item)->map(fn ($p) => $this->productData($p));

        return Inertia::render('SouvenirMarketplace', [...$this->shared($request), 'view' => 'product', 'reviews' => $this->publicReviews(SouvenirReview::where('souvenir_product_id', $item->id)), 'productId' => $item->slug, 'products' => $products, 'shops' => [$this->shopData($item->vendor)]])
            ->withViewData('seo', ['title' => $item->name, 'description' => Str::limit($item->description, 155), 'canonical' => route('souvenirs.show', $item->slug), 'robots' => 'index,follow']);
    }

    public function store(Request $request, Vendor $shop): Response
    {
        abort_unless($shop->status === 'verified' && $shop->user->status === 'active', 404);
        $records = SouvenirProduct::published()->with('vendor')->where('vendor_id', $shop->id)->orderByDesc('id')->cursorPaginate(24)->withQueryString();

        return Inertia::render('SouvenirMarketplace', [...$this->shared($request), 'view' => 'store', 'reviews' => $this->publicReviews(SouvenirReview::whereHas('product', fn ($q) => $q->where('vendor_id', $shop->id))), 'shopId' => (string) $shop->id, 'shops' => [$this->shopData($shop)], 'products' => $records->getCollection()->map(fn ($p) => $this->productData($p)), 'pagination' => ['previous' => $records->previousPageUrl(), 'next' => $records->nextPageUrl()]]);
    }

    public function cart(Request $request): Response
    {
        $lines = SouvenirCartItem::where('user_id', $request->user()->id)->with('product.vendor')->limit(50)->get();
        $products = $lines->pluck('product')->unique('id')->map(fn ($p) => $this->productData($p))->values();

        return Inertia::render('SouvenirMarketplace', [...$this->shared($request), 'view' => 'cart', 'products' => $products, 'shops' => $products->pluck('shop')->unique('id')->values(), 'cartItems' => $lines->map(fn ($line) => ['key' => $line->id, 'id' => $line->product->slug, 'variant' => $line->variant, 'quantity' => $line->quantity, 'note' => $line->note ?? ''])]);
    }

    public function addCart(Request $request): RedirectResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer'], 'variant' => ['required', 'string', 'max:100'], 'quantity' => ['required', 'integer', 'min:1', 'max:1000'], 'note' => ['nullable', 'string', 'max:500']]);
        DB::transaction(function () use ($request, $data): void {
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $product = SouvenirProduct::published()->findOrFail($data['product_id']);
            $query = SouvenirCartItem::where('user_id', $request->user()->id);
            $line = (clone $query)->where('souvenir_product_id', $product->id)->where('variant', $data['variant'])->first();
            $count = (clone $query)->where('souvenir_product_id', $product->id)->sum('quantity');
            if (! in_array($data['variant'], $product->variants, true) || $count + $data['quantity'] > $product->stock - $product->reserved_stock || (! $line && $query->count() >= 50)) {
                throw ValidationException::withMessages(['cart' => 'Varian, stok, atau batas keranjang tidak tersedia.']);
            }
            SouvenirCartItem::updateOrCreate(['user_id' => $request->user()->id, 'souvenir_product_id' => $product->id, 'variant' => $data['variant']], ['quantity' => ($line?->quantity ?? 0) + $data['quantity'], 'note' => $data['note'] ?? null]);
        }, 3);

        return back()->with('success', 'Produk masuk keranjang.');
    }

    public function updateCart(Request $request, SouvenirCartItem $line): RedirectResponse
    {
        abort_unless($line->user_id === $request->user()->id, 404);
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:1000']]);
        DB::transaction(function () use ($request, $line, $data): void {
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            if ($data['quantity'] === 0) {
                $line->delete();

                return;
            }
            $product = $line->product;
            $other = SouvenirCartItem::where('user_id', $line->user_id)->where('souvenir_product_id', $product->id)->whereKeyNot($line->id)->sum('quantity');
            if ($other + $data['quantity'] > $product->stock - $product->reserved_stock) {
                throw ValidationException::withMessages(['cart' => 'Stok tidak mencukupi.']);
            }
            $line->update($data);
        }, 3);

        return back();
    }

    public function order(SouvenirCheckoutRequest $request, SouvenirCommerceService $service): RedirectResponse
    {
        $order = $service->createOrder($request->user(), $request->validated());

        return $request->boolean('checkout_flow') ? to_route('checkout.payment', ['type' => 'souvenir', 'id' => $order->id]) : to_route('souvenirs.orders.show', $order);
    }

    public function orders(Request $request): Response
    {
        return Inertia::render('SouvenirOrders', ['orders' => SouvenirOrder::where('user_id', $request->user()->id)->with('items', 'payment', 'vendor:id,name')->latest('id')->cursorPaginate(20)]);
    }

    public function orderShow(Request $request, SouvenirOrder $order, SouvenirCommerceService $service): Response
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        if ($order->status === 'awaiting_payment' && $order->expires_at->isPast()) {
            $service->transition($order, 'expired');
            $order->refresh();
        }

        return Inertia::render('SouvenirOrders', ['order' => $order->load('items', 'payment', 'vendor:id,name'), 'paymentEnabled' => filled(config('platform.midtrans_server_key'))]);
    }

    public function pay(Request $request, SouvenirOrder $order, SouvenirCommerceService $service): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return Inertia::location($service->checkout($order));
    }

    public function transition(Request $request, SouvenirOrder $order, SouvenirCommerceService $service): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $data = $request->validate(['status' => ['required', 'in:cancelled,completed']]);
        $service->transition($order, $data['status']);

        return back();
    }

    public function saved(Request $request): RedirectResponse
    {
        $data = $request->validate(['kind' => ['required', 'in:product,store'], 'target_id' => ['required', 'integer']]);
        if ($data['kind'] === 'product') {
            SouvenirProduct::published()->findOrFail($data['target_id']);
        } else {
            Vendor::where('status', 'verified')->findOrFail($data['target_id']);
        }
        DB::transaction(function () use ($request, $data): void {
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $query = DB::table('souvenir_saved_items')->where('user_id', $request->user()->id)->where($data);
            if ($query->exists()) {
                $query->delete();
            } else {
                if (DB::table('souvenir_saved_items')->where('user_id', $request->user()->id)->count() >= 500) {
                    throw ValidationException::withMessages(['saved' => 'Maksimal 500 produk atau toko tersimpan. Hapus salah satu sebelum menambah.']);
                }
                DB::table('souvenir_saved_items')->insert(['user_id' => $request->user()->id, ...$data]);
            }
        });

        return back();
    }

    /** @return array<string, mixed> */
    private function shared(Request $request): array
    {
        $saved = $request->user() ? DB::table('souvenir_saved_items')->where('user_id', $request->user()->id)->limit(500)->get() : collect();

        return ['reviews' => [], 'cartCount' => $request->user() ? SouvenirCartItem::where('user_id', $request->user()->id)->sum('quantity') : 0, 'savedProducts' => $saved->where('kind', 'product')->pluck('target_id'), 'followedStores' => $saved->where('kind', 'store')->pluck('target_id')];
    }

    /** @return array<string, mixed> */
    private function productData(SouvenirProduct $product): array
    {
        return ['id' => $product->slug, 'databaseId' => $product->id, 'name' => $product->name, 'shopId' => (string) $product->vendor_id, 'shop' => $this->shopData($product->vendor), 'category' => $product->category, 'price' => $product->sellingPrice(), 'availability' => $product->availability, 'preparation' => 'Persiapan '.$product->preparation_days.' hari', 'preparationDays' => $product->preparation_days, 'weight' => $product->weight, 'stock' => max(0, $product->stock - $product->reserved_stock), 'pickupOnly' => $product->pickup_only, 'pickupAddress' => $product->pickup_address, 'deliveryRates' => $product->delivery_rates ?? [], 'variants' => $product->variants, 'description' => $product->description, 'care' => $product->care, 'imageUrl' => $product->image_url, 'status' => $product->status];
    }

    /** @return array<string, mixed> */
    private function publicReviews(Builder $query): array
    {
        return $query->where('status', 'published')->with('user:id,name')->latest('id')->limit(20)->get()->map(fn ($review) => ['id' => $review->id, 'buyer' => mb_substr($review->user->name, 0, 2).'***', 'rating' => $review->rating, 'body' => $review->body, 'vendor_response' => $review->vendor_response, 'created_at' => $review->created_at->toDateString()])->all();
    }

    /** @return array<string, mixed> */
    private function shopData(Vendor $vendor): array
    {
        return ['id' => (string) $vendor->id, 'name' => $vendor->name, 'city' => $vendor->city, 'description' => $vendor->description, 'initials' => mb_substr($vendor->name, 0, 2)];
    }
}
