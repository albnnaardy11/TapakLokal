<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use App\Models\Vendor;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('content.manage') || $request->user()->hasPermission('vendor.access'), 403);
        $data = $request->validate(['file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'], 'alt_text' => ['nullable', 'string', 'max:255'], 'visibility' => ['required', Rule::in(['public', 'private'])]]);
        abort_if($data['visibility'] === 'public' && ! $request->user()->hasPermission('content.manage'), 403);
        if ($data['visibility'] === 'public') {
            $request->validate(['file' => ['image', 'mimes:jpg,jpeg,png,webp']]);
        }
        $file = $request->file('file');
        $path = $file->store('media', 'local');
        try {
            $media = DB::transaction(function () use ($request, $file, $path, $data, $audit) {
                $media = MediaAsset::create(['user_id' => $request->user()->id, 'name' => mb_substr($file->getClientOriginalName(), 0, 200), 'disk' => 'local', 'path' => $path, 'mime_type' => $file->getMimeType(), 'size' => $file->getSize(), 'alt_text' => $data['alt_text'] ?? null, 'visibility' => $data['visibility']]);
                $audit->record('media.uploaded', $media, ['visibility' => $media->visibility]);

                return $media;
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        return back()->with('success', 'Media tersimpan. ID: '.$media->id.' · '.route('media.show', $media));
    }

    public function show(Request $request, MediaAsset $media): StreamedResponse
    {
        $user = $request->user();
        $canVerify = $user?->hasPermission('vendor.verify') && Vendor::where('document_id', $media->id)->exists();
        abort_unless($media->visibility === 'public' || $user && ($media->user_id === $user->id || $canVerify), 404);

        return Storage::disk($media->disk)->response($media->path, $media->name, ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => $media->visibility === 'public' ? 'public, max-age=3600' : 'private, no-store'], $media->visibility === 'public' ? 'inline' : 'attachment');
    }
}
