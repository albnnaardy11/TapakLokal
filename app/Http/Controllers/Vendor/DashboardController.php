<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MediaAsset;
use App\Models\Payout;
use App\Models\Review;
use App\Models\SupportTicket;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, string $section = 'dashboard'): Response
    {
        abort_unless(in_array($section, ['dashboard', 'trips', 'bookings', 'finance', 'profile', 'reviews', 'support']), 404);
        $vendor = Vendor::where('user_id', $request->user()->id)->first();
        $records = null;
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:180'], 'status' => ['nullable', 'in:draft,pending,published,rejected,archived']]);
        $tripCounts = $vendor ? Trip::where('vendor_id', $vendor->id)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status') : [];
        if ($vendor) {
            $query = match ($section) {
                'trips' => Trip::where('vendor_id', $vendor->id),
                'bookings' => Booking::with(['trip:id,title', 'corporateRequest:id,booking_id,corporate_company_id', 'corporateRequest.company:id,name'])->where('vendor_id', $vendor->id),
                'finance' => Payout::where('vendor_id', $vendor->id),
                'reviews' => Review::with('trip:id,title')->whereHas('trip', fn ($q) => $q->where('vendor_id', $vendor->id)),
                'support' => SupportTicket::where(fn ($q) => $q->where('user_id', $request->user()->id)->orWhere('vendor_id', $vendor->id)),
                default => null,
            };
            if ($section === 'trips') {
                if (! empty($filters['search'])) {
                    $query->where(fn ($query) => $query->whereLike('title', '%'.$filters['search'].'%')->orWhereLike('destination', '%'.$filters['search'].'%'));
                }
                if (! empty($filters['status'])) {
                    $query->where('status', $filters['status']);
                }
            }
            $records = $query?->orderByDesc('id')->paginate(15)->withQueryString();
        }

        return Inertia::render('Vendor/Dashboard', [
            'section' => $section, 'vendor' => $vendor, 'records' => $records,
            'filters' => $filters, 'tripCounts' => $tripCounts,
            'stats' => $vendor ? [
                ['label' => 'Trip Anda', 'value' => Trip::where('vendor_id', $vendor->id)->count()],
                ['label' => 'Pemesanan', 'value' => Booking::where('vendor_id', $vendor->id)->count()],
                ['label' => 'Payout dibayar', 'value' => Payout::where('vendor_id', $vendor->id)->where('status', 'paid')->sum('amount'), 'money' => true],
            ] : [],
        ]);
    }

    public function saveProfile(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:180'], 'city' => ['required', 'string', 'max:100'], 'email' => ['required', 'email'], 'phone' => ['required', 'string', 'max:30'], 'description' => ['nullable', 'string', 'max:5000'], 'document_id' => ['nullable', 'integer', 'exists:media_assets,id']]);
        if (! empty($data['document_id'])) {
            abort_unless(MediaAsset::whereKey($data['document_id'])->where('user_id', $request->user()->id)->where('visibility', 'private')->exists(), 404);
        }
        DB::transaction(function () use ($request, $data, $audit) {
            User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $vendor = Vendor::where('user_id', $request->user()->id)->lockForUpdate()->first() ?? new Vendor(['user_id' => $request->user()->id]);
            abort_if($vendor->status === 'suspended', 403);
            $vendor->fill($data);
            if ($vendor->isDirty(['name', 'email', 'document_id']) || ! $vendor->exists) {
                $vendor->status = 'pending';
            }
            $vendor->save();
            $audit->record('vendor.profile_updated', $vendor, ['status' => $vendor->status]);
        });

        return back()->with('success', 'Profil tersimpan. Perubahan identitas memerlukan verifikasi ulang.');
    }
}
