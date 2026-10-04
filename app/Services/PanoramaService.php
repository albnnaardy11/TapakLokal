<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PanoramaService
{
    public function thumbnail(MediaAsset $media): string
    {
        $disk = Storage::disk($media->disk);
        $namedThumbnail = 'panoramas/thumbnails/'.pathinfo($media->path, PATHINFO_FILENAME).'.webp';
        if ($disk->exists($namedThumbnail)) {
            return $namedThumbnail;
        }

        $path = 'panoramas/thumbnails/'.hash('sha256', $media->path.'|'.$disk->lastModified($media->path)).'.webp';
        if (! $disk->exists($path)) {
            $input = imagecreatefromstring($disk->get($media->path));
            if (! $input) {
                throw new \RuntimeException('Panorama tidak dapat dibaca.');
            }
            $width = min(480, imagesx($input));
            $output = imagescale($input, $width);
            $stream = fopen('php://temp', 'w+b');
            try {
                if (! $output || ! imagewebp($output, $stream, 75)) {
                    throw new \RuntimeException('Thumbnail gagal dibuat.');
                }
                rewind($stream);
                if (! $disk->put($path, $stream)) {
                    throw new \RuntimeException('Thumbnail gagal disimpan.');
                }
            } finally {
                fclose($stream);
                imagedestroy($input);
                if ($output) {
                    imagedestroy($output);
                }
            }
        }

        return $path;
    }

    public function import(string $source, string $name, User $user): MediaAsset
    {
        $size = getimagesize($source);
        if (! $size || ! in_array($size[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP]) || $size[0] < 1024 || $size[0] > 16000 || $size[1] > 8000 || $size[0] * $size[1] > 60000000 || ($size[0] / $size[1]) < 1.75) {
            throw ValidationException::withMessages(['file' => 'Gunakan panorama JPG/PNG/WebP dengan rasio minimal 1.75:1 dan berukuran 1024–16000 piksel.']);
        }

        $input = match ($size[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($source),
            IMAGETYPE_PNG => imagecreatefrompng($source),
            IMAGETYPE_WEBP => imagecreatefromwebp($source),
        };
        if (! $input) {
            throw ValidationException::withMessages(['file' => 'Foto tidak dapat dibaca.']);
        }

        $outputWidth = min(2560, $size[0]);
        $outputHeight = (int) round($outputWidth / 2);
        $output = imagecreatetruecolor($outputWidth, $outputHeight);

        // Fill full 2:1 frame without dark/black padding bars
        imagecopyresampled($output, $input, 0, 0, 0, 0, $outputWidth, $outputHeight, $size[0], $size[1]);

        $stream = fopen('php://temp', 'w+b');
        try {
            if (! imagewebp($output, $stream, 80)) {
                throw new \RuntimeException('Gagal mengoptimalkan panorama.');
            }
            rewind($stream);
            $path = 'panoramas/'.Str::uuid().'.webp';
            if (! Storage::disk('local')->put($path, $stream)) {
                throw new \RuntimeException('Gagal menyimpan panorama.');
            }
            try {
                return MediaAsset::create([
                    'user_id' => $user->id,
                    'name' => Str::limit(pathinfo($name, PATHINFO_FILENAME), 160, '').'.webp',
                    'disk' => 'local',
                    'path' => $path,
                    'mime_type' => 'image/webp',
                    'size' => Storage::disk('local')->size($path),
                    'visibility' => 'private',
                    'alt_text' => $name,
                ]);
            } catch (\Throwable $exception) {
                Storage::disk('local')->delete($path);
                throw $exception;
            }
        } finally {
            fclose($stream);
            imagedestroy($input);
            imagedestroy($output);
        }
    }
}
