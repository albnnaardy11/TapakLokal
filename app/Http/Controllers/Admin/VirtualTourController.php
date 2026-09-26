<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VirtualTourRequest;
use App\Models\ContentPage;
use App\Models\Trip;
use App\Models\VirtualTour;
use App\Services\AuditService;
use App\Services\BackofficeRegistry;
use App\Services\PanoramaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VirtualTourController extends Controller
{
    public function index(Request $request, BackofficeRegistry $registry): Response
    {
        abort_unless($request->user()->hasPermission('content.view'), 403);

        return Inertia::render('Admin/VirtualTours', ['navigation' => $registry->navigation($request->user()), 'tours' => VirtualTour::with(['trip:id,title', 'contentPage:id,title'])->orderBy('position')->orderBy('id')->paginate(20)->through(fn (VirtualTour $tour) => [...$tour->only(['id', 'title', 'slug', 'description', 'status', 'placement', 'trip_id', 'content_page_id', 'position']), 'target_title' => $tour->trip?->title ?? $tour->contentPage?->title, 'preview' => $tour->presentation(true)])]);
    }

    public function targets(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasPermission('content.view'), 403);
        $data = $request->validate(['type' => ['required', 'in:trip,destination'], 'q' => ['nullable', 'string', 'max:100']]);
        $query = $data['type'] === 'trip' ? Trip::query() : ContentPage::whereIn('type', ['destination', 'hidden-gem']);

        return response()->json($query->when($data['q'] ?? null, fn ($query, $q) => $query->where('title', 'like', '%'.$q.'%'))->orderByDesc('id')->limit(20)->get(['id', 'title', 'status']));
    }

    public function save(VirtualTourRequest $request, PanoramaService $panoramas, AuditService $audit, ?VirtualTour $tour = null): RedirectResponse
    {
        $data = $request->safe()->except('file');
        $data['trip_id'] = $data['placement'] === 'trip' ? $data['trip_id'] : null;
        $data['content_page_id'] = $data['placement'] === 'destination' ? $data['content_page_id'] : null;
        $media = null;
        try {
            DB::transaction(function () use ($request, $panoramas, $audit, $tour, $data, &$media) {
                if ($request->hasFile('file')) {
                    $media = $panoramas->import($request->file('file')->getRealPath(), $request->file('file')->getClientOriginalName(), $request->user());
                    $data['media_asset_id'] = $media->id;
                }
                $record = $tour ?? new VirtualTour;
                $before = $record->only(['status', 'placement', 'trip_id', 'content_page_id']);
                $record->fill($data)->save();
                $audit->record('virtual_tour.saved', $record, ['before' => $before, 'after' => $record->only(['status', 'placement', 'trip_id', 'content_page_id'])]);
            });
        } catch (\Throwable $exception) {
            if ($media) {
                Storage::disk($media->disk)->delete($media->path);
            }
            throw $exception;
        }

        return to_route('admin.tours.index')->with('success', 'Panorama tersimpan. Penempatan mengikuti status publikasi halaman tujuan.');
    }

    public function image(VirtualTour $tour): StreamedResponse
    {
        abort_unless(VirtualTour::visible()->whereKey($tour->id)->exists(), 404);

        return $this->stream($tour);
    }

    public function preview(Request $request, VirtualTour $tour): StreamedResponse
    {
        abort_unless($request->user()->hasPermission('content.view'), 403);

        return $this->stream($tour);
    }

    private function stream(VirtualTour $tour): StreamedResponse
    {
        $media = $tour->media;

        return Storage::disk($media->disk)->response($media->path, $media->name, ['Content-Type' => 'image/webp', 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store'], 'inline');
    }
}
