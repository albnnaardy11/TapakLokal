<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payout;
use App\Models\Refund;
use App\Models\Trip;
use App\Models\Vendor;
use App\Services\AuditService;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class WorkflowController extends Controller
{
    public function __invoke(Request $request, string $module, string $record, string $action, AuditService $audit, BookingService $bookings): RedirectResponse
    {
        if ($module === 'bookings') {
            Gate::authorize('operations.manage');
            $target = ['confirm' => 'confirmed', 'start' => 'ongoing', 'complete' => 'completed', 'cancel' => 'cancelled'][$action] ?? abort(404);
            $bookings->transition(Booking::findOrFail($record), $target);

            return back()->with('success', 'Status pemesanan diperbarui.');
        }
        $permission = match ($module) {
            'vendors' => 'vendor.verify', 'trips' => 'operations.manage', 'refunds' => 'refund.approve', 'payouts' => 'payout.approve', default => abort(404)
        };
        Gate::authorize($permission);
        $request->validate(['note' => ['nullable', 'string', 'max:2000']]);
        DB::transaction(function () use ($module, $record, $action, $request, $audit) {
            $class = ['vendors' => Vendor::class, 'trips' => Trip::class, 'refunds' => Refund::class, 'payouts' => Payout::class][$module];
            if (in_array($module, ['refunds', 'payouts'])) {
                $initial = $class::findOrFail($record);
                $booking = Booking::whereKey($initial->booking_id)->lockForUpdate()->firstOrFail();
            }
            $item = $class::lockForUpdate()->findOrFail($record);
            $transitions = match ($module) {
                'vendors' => ['pending' => ['verify' => 'verified', 'reject' => 'rejected'], 'rejected' => ['verify' => 'verified'], 'verified' => ['suspend' => 'suspended'], 'suspended' => ['verify' => 'verified']],
                'trips' => ['pending' => ['publish' => 'published', 'reject' => 'rejected'], 'published' => ['archive' => 'archived'], 'rejected' => ['archive' => 'archived']],
                'refunds' => ['pending' => ['approve' => 'approved', 'reject' => 'rejected']],
                'payouts' => ['eligible' => ['approve' => 'pending']],
            };
            $target = $transitions[$item->status][$action] ?? null;
            if (! $target) {
                throw ValidationException::withMessages(['status' => 'Aksi tidak sesuai status saat ini.']);
            }
            if ($module === 'trips' && $action === 'publish' && Vendor::whereKey($item->vendor_id)->lockForUpdate()->firstOrFail()->status !== 'verified') {
                throw ValidationException::withMessages(['status' => 'Vendor harus terverifikasi sebelum trip diterbitkan.']);
            }
            if ($module === 'vendors' && $action === 'verify' && ! $item->document_id) {
                throw ValidationException::withMessages(['status' => 'Dokumen verifikasi harus dilengkapi terlebih dahulu.']);
            }
            if ($module === 'refunds' && $action === 'approve') {
                if ($item->user_id === $request->user()->id || $booking->payment?->status !== 'paid' || $booking->payout && in_array($booking->payout->status, ['pending', 'processing', 'paid'])) {
                    throw ValidationException::withMessages(['status' => 'Refund tidak memenuhi syarat persetujuan atau memerlukan pemeriksa lain.']);
                }
                $item->approved_by = $request->user()->id;
            }
            if ($module === 'payouts') {
                if ($booking->status !== 'completed' || $booking->payment?->status !== 'paid' || $booking->refund && $booking->refund->status !== 'rejected' || $item->vendor->user_id === $request->user()->id) {
                    throw ValidationException::withMessages(['status' => 'Payout belum memenuhi syarat atau memerlukan pemeriksa lain.']);
                }
                $item->approved_by = $request->user()->id;
            }
            if ($module === 'vendors') {
                $item->verification_note = $request->input('note');
            }
            $before = $item->status;
            $item->status = $target;
            $item->save();
            $audit->record($module.'.'.$action, $item, ['before' => $before, 'after' => $target, 'note' => $request->input('note')]);
        });

        return back()->with('success', in_array($module, ['refunds', 'payouts']) ? 'Persetujuan tercatat. Dana belum dikirim; eksekusi gateway perlu dikonfigurasi.' : 'Status berhasil diperbarui.');
    }
}
