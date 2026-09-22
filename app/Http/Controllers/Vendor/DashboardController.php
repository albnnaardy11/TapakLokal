<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Payout, Review, Trip, Vendor};
use App\Services\AuditService;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\DB;
use Inertia\{Inertia, Response};

class DashboardController extends Controller
{
    public function index(Request $request, string $section = 'dashboard'): Response
    {
        abort_unless(in_array($section, ['dashboard', 'trips', 'bookings', 'finance', 'profile', 'reviews', 'support']), 404);
        $vendor = Vendor::where('user_id', $request->user()->id)->first();
        $records = null;
        if ($vendor) {
            $query = match ($section) {
                'trips' => Trip::where('vendor_id', $vendor->id),
                'bookings' => Booking::with('trip:id,title')->where('vendor_id', $vendor->id),
                'finance' => Payout::where('vendor_id', $vendor->id),
                'reviews' => Review::with('trip:id,title')->whereHas('trip', fn ($q) => $q->where('vendor_id', $vendor->id)),
                'support' => \App\Models\SupportTicket::where(fn ($q) => $q->where('user_id', $request->user()->id)->orWhere('vendor_id', $vendor->id)),
                default => null,
            };
            $records = $query?->orderByDesc('id')->paginate(15);
        }
        return Inertia::render('Vendor/Dashboard', [
            'section' => $section, 'vendor' => $vendor, 'records' => $records,
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
            abort_unless(\App\Models\MediaAsset::whereKey($data['document_id'])->where('user_id', $request->user()->id)->where('visibility', 'private')->exists(), 404);
        }
        DB::transaction(function () use ($request, $data, $audit) {
            \App\Models\User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            $vendor = Vendor::where('user_id', $request->user()->id)->lockForUpdate()->first() ?? new Vendor(['user_id' => $request->user()->id]);
            abort_if($vendor->status === 'suspended', 403);
            $vendor->fill($data);
            if ($vendor->isDirty(['name', 'email', 'document_id']) || ! $vendor->exists) { $vendor->status = 'pending'; }
            $vendor->save();
            $audit->record('vendor.profile_updated', $vendor, ['status' => $vendor->status]);
        });
        return back()->with('success', 'Profil tersimpan. Perubahan identitas memerlukan verifikasi ulang.');
    }
}
