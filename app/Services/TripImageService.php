<?php

namespace App\Services;

use App\Models\Trip;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TripImageService
{
    public function thumbnailUrl(Trip $trip): ?string
    {
        $sourcePath = $this->publicSourcePath($trip);
        if (! $sourcePath) {
            return null;
        }

        $disk = Storage::disk('public');

        return route('trips.thumbnail', [
            'trip' => $trip,
            'v' => hash('sha256', $sourcePath.'|'.$disk->lastModified($sourcePath).'|'.$disk->size($sourcePath)),
        ]);
    }

    public function publicSourcePath(Trip $trip): ?string
    {
        $urlPath = parse_url((string) $trip->image_url, PHP_URL_PATH);
        if (! is_string($urlPath) || ! Str::startsWith($urlPath, '/storage/')) {
            return null;
        }

        $path = Str::after($urlPath, '/storage/');

        return $path !== '' && Storage::disk('public')->exists($path) ? $path : null;
    }

    public function thumbnail(Trip $trip): ?string
    {
        $sourcePath = $this->publicSourcePath($trip);
        if (! $sourcePath) {
            return null;
        }

        $disk = Storage::disk('public');
        $path = 'trips/thumbnails/'.hash('sha256', $sourcePath.'|'.$disk->lastModified($sourcePath)).'.webp';
        if ($disk->exists($path)) {
            return $path;
        }

        $input = imagecreatefromstring($disk->get($sourcePath));
        if (! $input) {
            return null;
        }

        $width = min(480, imagesx($input));
        $output = imagescale($input, $width);
        $stream = fopen('php://temp', 'w+b');
        try {
            if (! $output || ! imagewebp($output, $stream, 72)) {
                return null;
            }

            rewind($stream);
            if (! $disk->put($path, $stream)) {
                return null;
            }

            return $path;
        } finally {
            fclose($stream);
            imagedestroy($input);
            if ($output) {
                imagedestroy($output);
            }
        }
    }
}
