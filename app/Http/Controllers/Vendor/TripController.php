<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\TripRequest;
use App\Models\MediaAsset;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TripController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Vendor/TripForm', ['trip' => null]);
    }

    public function edit(Request $request, Trip $trip): Response
    {
        abort_unless($trip->vendor->user_id === $request->user()->id, 404);

        return Inertia::render('Vendor/TripForm', ['trip' => $trip]);
    }

    public function store(TripRequest $request, AuditService $audit): RedirectResponse
    {
        $vendor = Vendor::where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($vendor->status === 'verified', 403, 'Vendor harus terverifikasi.');
        DB::transaction(function () use ($request, $vendor, $audit) {
            $vendor = Vendor::whereKey($vendor->id)->lockForUpdate()->firstOrFail();
            abort_unless($vendor->status === 'verified', 403);
            $trip = Trip::create([...$request->validated(), 'vendor_id' => $vendor->id]);
            $audit->record('trip.created', $trip);
            $this->notifyReviewers($trip);
        });

        return to_route('vendor.section', 'trips')->with('success', $request->validated('status') === 'pending' ? 'Trip berhasil dikirim ke admin operasional untuk ditinjau.' : 'Draf trip tersimpan.');
    }

    public function update(TripRequest $request, Trip $trip, AuditService $audit): RedirectResponse
    {
        abort_unless($trip->vendor->user_id === $request->user()->id, 404);
        abort_unless($trip->vendor->status === 'verified', 403);
        DB::transaction(function () use ($request, $trip, $audit) {
            $trip = Trip::whereKey($trip->id)->lockForUpdate()->firstOrFail();
            $vendor = Vendor::whereKey($trip->vendor_id)->lockForUpdate()->firstOrFail();
            abort_unless($vendor->status === 'verified', 403);
            if ($trip->bookings()->exists()) {
                throw ValidationException::withMessages(['title' => 'Trip yang sudah memiliki pesanan tidak dapat diubah. Buat jadwal baru atau hubungi operasional.']);
            }
            $trip->update($request->validated());
            $audit->record('trip.updated', $trip);
            $this->notifyReviewers($trip);
        });

        return to_route('vendor.section', 'trips')->with('success', $request->validated('status') === 'pending' ? 'Trip berhasil dikirim ke admin operasional untuk ditinjau.' : 'Draf trip diperbarui.');
    }

    private function notifyReviewers(Trip $trip): void
    {
        if ($trip->status !== 'pending') {
            return;
        }
        User::where('status', 'active')->whereHas('roles.permissions', fn ($query) => $query->where('name', 'operations.manage'))
            ->with('roles.permissions')->each(function (User $admin) use ($trip): void {
                if (! $admin->hasPermission('admin.access') || ! $admin->hasPermission('operations.view')) {
                    return;
                }
                $admin->notifications()->create([
                    'id' => (string) Str::uuid7(), 'type' => 'trip.submitted',
                    'data' => ['title' => 'Trip menunggu peninjauan', 'reference' => $trip->title,
                        'url' => route('admin.panel.resources.show', ['panel' => 'operations', 'module' => 'trips', 'record' => $trip->id], false)],
                ]);
            });
    }

    public function destroy(Request $request, Trip $trip, AuditService $audit): RedirectResponse
    {
        abort_unless($trip->vendor->user_id === $request->user()->id, 404);
        DB::transaction(function () use ($trip, $audit) {
            $trip = Trip::whereKey($trip->id)->lockForUpdate()->firstOrFail();
            if ($trip->bookings()->exists()) {
                throw ValidationException::withMessages(['delete' => 'Trip yang memiliki pesanan tidak dapat dihapus. Hubungi operasional untuk mengarsipkan jadwal.']);
            }
            $audit->record('trip.deleted', $trip, ['title' => $trip->title]);
            $trip->delete();
        });

        return to_route('vendor.section', 'trips')->with('success', 'Trip dihapus dari katalog.');
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:15360'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('image');
        $filename = 'trip-'.(string) Str::ulid().'.webp';
        $storagePath = 'trips/'.$filename;

        Storage::disk('public')->makeDirectory('trips');

        // Check if image is already WebP (e.g. from client-side conversion)
        if ($file->getMimeType() === 'image/webp' || strtolower($file->getClientOriginalExtension()) === 'webp') {
            $webpData = file_get_contents($file->getRealPath());
        } else {
            // Server-side fallback WebP conversion & compression via GD
            $extension = strtolower($file->getClientOriginalExtension());
            $img = match ($extension) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($file->getRealPath()),
                'png' => @imagecreatefrompng($file->getRealPath()),
                'webp' => @imagecreatefromwebp($file->getRealPath()),
                default => @imagecreatefromstring(file_get_contents($file->getRealPath())),
            };

            if (! $img) {
                $img = @imagecreatefromstring(file_get_contents($file->getRealPath()));
            }

            if ($img) {
                $origWidth = imagesx($img);
                $origHeight = imagesy($img);
                $maxWidth = 1600;

                if ($origWidth > $maxWidth) {
                    $newWidth = $maxWidth;
                    $newHeight = (int) round(($origHeight / $origWidth) * $newWidth);
                    $resized = imagecreatetruecolor($newWidth, $newHeight);
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                    imagedestroy($img);
                    $img = $resized;
                }

                ob_start();
                imagewebp($img, null, 88);
                $webpData = ob_get_clean();
                imagedestroy($img);
            } else {
                $webpData = file_get_contents($file->getRealPath());
            }
        }

        Storage::disk('public')->put($storagePath, $webpData);

        try {
            MediaAsset::create([
                'user_id' => $request->user()->id,
                'name' => $filename,
                'disk' => 'public',
                'path' => $storagePath,
                'mime_type' => 'image/webp',
                'size' => strlen($webpData),
                'alt_text' => $request->input('alt_text', 'Foto Trip'),
                'visibility' => 'public',
            ]);
        } catch (\Throwable) {
            // Non-critical tracking
        }

        return response()->json([
            'success' => true,
            'url' => asset('storage/'.$storagePath),
            'filename' => $filename,
            'size' => strlen($webpData),
            'mime_type' => 'image/webp',
        ]);
    }
}
