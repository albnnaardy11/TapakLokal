<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VirtualTour;
use App\Services\AuditService;
use App\Services\PanoramaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportVirtualTours extends Command
{
    protected $signature = 'platform:import-tours {directory} {--owner= : Email content admin} {--force : Re-import and overwrite existing media}';

    protected $description = 'Import panorama lokal sebagai draft tanpa mengubah foto sumber atau penempatan yang sudah diatur admin';

    public function handle(PanoramaService $panoramas, AuditService $audit): int
    {
        $owner = User::where('email', $this->option('owner'))->first() ?? User::where('status', 'active')->first() ?? User::first();
        if (! $owner || ! is_dir($this->argument('directory'))) {
            $this->error('Folder atau akun pemilik tidak valid.');

            return self::FAILURE;
        }
        $failed = false;
        foreach (new \DirectoryIterator($this->argument('directory')) as $file) {
            if (! $file->isFile() || ! in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
                continue;
            }
            if ($file->getSize() > 25600 * 1024) {
                $this->error($file->getFilename().': melebihi 25 MB.');
                $failed = true;

                continue;
            }
            $hash = hash_file('sha256', $file->getPathname());
            $existing = VirtualTour::withTrashed()->where('source_hash', $hash)->first();
            if ($existing && ! $this->option('force')) {
                $this->line('Sudah diimpor: '.$file->getFilename());

                continue;
            }
            $media = null;
            try {
                DB::transaction(function () use ($file, $hash, $owner, $panoramas, $audit, $existing, &$media) {
                    $media = $panoramas->import($file->getPathname(), $file->getFilename(), $owner);
                    if ($existing) {
                        $oldMedia = $existing->media;
                        $existing->update(['media_asset_id' => $media->id]);
                        if ($oldMedia) {
                            Storage::disk($oldMedia->disk)->delete($oldMedia->path);
                            $oldMedia->delete();
                        }
                    } else {
                        $title = Str::headline($file->getBasename('.'.$file->getExtension()));
                        $tour = VirtualTour::create(['title' => Str::limit($title, 180, ''), 'slug' => Str::limit(Str::slug($title), 150, '').'-'.substr($hash, 0, 12), 'media_asset_id' => $media->id, 'status' => 'published', 'placement' => 'homepage', 'source_hash' => $hash]);
                        $audit->record('virtual_tour.imported', $tour, ['source' => $file->getFilename()], $owner->id);
                    }
                });
                $this->info('Panorama diproses: '.$file->getFilename());
            } catch (\Throwable $exception) {
                if ($media) {
                    Storage::disk($media->disk)->delete($media->path);
                }
                $this->error($file->getFilename().': '.$exception->getMessage());
                $failed = true;
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
