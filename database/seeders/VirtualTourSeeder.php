<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Models\User;
use App\Models\VirtualTour;
use App\Services\AuditService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class VirtualTourSeeder extends Seeder
{
    /**
     * Data konfigurasi panorama 360° yang dioptimasi untuk SEO, Aksesibilitas, dan Performa tinggi.
     *
     * @var array<string, array{title: string, slug: string, description: string, alt: string}>
     */
    protected array $catalog = [
        'curug.jpg' => [
            'title' => 'Kawasan Curug Alami',
            'slug' => 'kawasan-curug-alami',
            'description' => 'Panorama 360° pemandangan alami air terjun jernih dengan suasana hutan pegunungan yang asri dan sejuk di Jawa Barat.',
            'alt' => 'Panorama 360 derajat suasana kawasan wisata air terjun alami dan pepohonan hijau rindang',
        ],
        'curung cinangka.jpg' => [
            'title' => 'Curug Cinangka',
            'slug' => 'curug-cinangka',
            'description' => 'Eksplorasi virtual 360° Curug Cinangka yang menampilkan keindahan tebing batuan alami dan aliran air terjun tersembunyi yang eksotis.',
            'alt' => 'Panorama 360 derajat tebing batu dan aliran air Curug Cinangka yang eksotis',
        ],
        'curung-cidaun.jpg' => [
            'title' => 'Curug Cidaun',
            'slug' => 'curug-cidaun',
            'description' => 'Tampilan interaktif 360° Curug Cidaun dengan panorama air terjun bertingkat yang menyejukkan di tengah lanskap alam terbuka.',
            'alt' => 'Panorama 360 derajat pemandangan air terjun bertingkat Curug Cidaun',
        ],
        'mt-gede-basecamp.jpg' => [
            'title' => 'Basecamp Gunung Gede',
            'slug' => 'basecamp-gunung-gede',
            'description' => 'Sudut pandang 360° area basecamp pendakian Gunung Gede Pangrango, tempat istirahat dan registrasi para pendaki sebelum menuju puncak.',
            'alt' => 'Panorama 360 derajat area basecamp pendakian Gunung Gede Pangrango',
        ],
        'mt-gede-parkiran-atas.jpg' => [
            'title' => 'Area Parkir Atas Gunung Gede',
            'slug' => 'parkiran-atas-gunung-gede',
            'description' => 'Panorama 360° area parkir atas jalur pendakian Gunung Gede dengan latar pemandangan bukit dan akses langsung ke pos gerbang.',
            'alt' => 'Panorama 360 derajat area parkir atas jalur pendakian Gunung Gede dengan lanskap perbukitan',
        ],
        'mt-gede-parkiran-basecamp.jpg' => [
            'title' => 'Parkiran Basecamp Gunung Gede',
            'slug' => 'parkiran-basecamp-gunung-gede',
            'description' => 'Tinjauan 360° fasilitas parkir kendaraan di sekitar basecamp Gunung Gede yang luas, aman, dan mudah diakses pengunjung.',
            'alt' => 'Panorama 360 derajat fasilitas parkir kendaraan di sekitar basecamp Gunung Gede',
        ],
        'mt-gede-parkiran-bawah.jpg' => [
            'title' => 'Area Parkir Bawah Gunung Gede',
            'slug' => 'parkiran-bawah-gunung-gede',
            'description' => 'Eksplorasi 360° area parkir bawah Gunung Gede sebagai titik temu awal kedatangan rombongan pendaki dan wisatawan lokal.',
            'alt' => 'Panorama 360 derajat area parkir bawah sebagai titik temu kedatangan pendaki Gunung Gede',
        ],
        'mt-gede-pintu rimba&regis.jpg' => [
            'title' => 'Pintu Rimba & Pos Registrasi Gede',
            'slug' => 'pintu-rimba-pos-registrasi-gede',
            'description' => 'Panorama 360° gerbang pintu rimba dan pos pemeriksaan resmi Taman Nasional Gunung Gede Pangrango sebelum memasuki hutan lindung.',
            'alt' => 'Panorama 360 derajat gerbang pintu rimba dan pos pemeriksaan Taman Nasional Gunung Gede Pangrango',
        ],
        'mt-salak-parkiran.jpg' => [
            'title' => 'Area Parkir Jalur Salak',
            'slug' => 'area-parkir-jalur-salak',
            'description' => 'Panorama 360° area parkir kedatangan pendakian Gunung Salak, dikelilingi rindangnya pepohonan pinus dan udara sejuk pegunungan.',
            'alt' => 'Panorama 360 derajat area parkir kedatangan pendakian Gunung Salak di bawah pohon pinus',
        ],
        'mt-salak-pintu-rimba-ceksampah.jpg' => [
            'title' => 'Pintu Rimba & Pos Sampah Salak',
            'slug' => 'pintu-rimba-pos-sampah-salak',
            'description' => 'Tinjauan 360° titik pintu rimba jalur pendakian Salak serta pos edukasi kelestarian lingkungan untuk pemeriksaan sampah pendaki.',
            'alt' => 'Panorama 360 derajat pintu rimba jalur pendakian Gunung Salak dan pos kelestarian lingkungan',
        ],
        'mt-salak-regist-toilet.jpg' => [
            'title' => 'Pos Registrasi & Fasilitas Salak',
            'slug' => 'pos-registrasi-fasilitas-salak',
            'description' => 'Suasana 360° pos registrasi, loket simaksi, dan fasilitas umum di titik awal jalur pendakian Gunung Salak.',
            'alt' => 'Panorama 360 derajat pos registrasi simaksi dan fasilitas umum di titik awal jalur Gunung Salak',
        ],
        'parkiran-curug.jpg' => [
            'title' => 'Area Parkir Wisata Curug',
            'slug' => 'area-parkir-wisata-curug',
            'description' => 'Panorama 360° area parkir dan gerbang masuk kawasan wisata air terjun dengan akses jalan yang mudah dan tertata rapi.',
            'alt' => 'Panorama 360 derajat area parkir dan pintu masuk kawasan wisata air terjun',
        ],
    ];

    public function run(?AuditService $audit = null): void
    {
        $audit ??= app(AuditService::class);
        $owner = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'content_admin']))->first()
            ?? User::first();

        if (! $owner) {
            $this->command?->warn('Tidak ada user admin untuk pemilik aset panorama.');

            return;
        }

        $sourceDir = public_path('Assets/Images/360');
        if (! is_dir($sourceDir)) {
            $this->command?->warn('Folder sumber panorama 360 tidak ditemukan: '.$sourceDir);

            return;
        }

        // Pastikan direktori storage public untuk panoramas dan thumbnails sudah ada
        Storage::disk('public')->makeDirectory('panoramas/thumbnails');

        $position = 0;
        foreach ($this->catalog as $filename => $meta) {
            $sourcePath = $sourceDir.DIRECTORY_SEPARATOR.$filename;
            if (! is_file($sourcePath)) {
                continue;
            }

            $sourceHash = hash_file('sha256', $sourcePath);
            $slug = $meta['slug'];

            // 1. Buat / perbarui Panorama WebP resolusi teroptimasi (2048x1024, WebP ~300-450KB)
            $publicPanoRelPath = 'panoramas/'.$slug.'.webp';
            $publicThumbRelPath = 'panoramas/thumbnails/'.$slug.'.webp';

            if (! Storage::disk('public')->exists($publicPanoRelPath) || ! Storage::disk('public')->exists($publicThumbRelPath)) {
                $this->optimizeAndStorePanorama($sourcePath, $publicPanoRelPath, $publicThumbRelPath);
            }

            $fileSize = Storage::disk('public')->size($publicPanoRelPath);

            // 2. Simpan / perbarui MediaAsset
            $media = MediaAsset::updateOrCreate(
                [
                    'disk' => 'public',
                    'path' => $publicPanoRelPath,
                ],
                [
                    'user_id' => $owner->id,
                    'name' => $slug.'.webp',
                    'mime_type' => 'image/webp',
                    'size' => $fileSize,
                    'visibility' => 'public',
                    'alt_text' => $meta['alt'],
                ]
            );

            // 3. Simpan / perbarui VirtualTour
            $tour = VirtualTour::withTrashed()->where('source_hash', $sourceHash)->orWhere('slug', $slug)->first()
                ?? new VirtualTour;
            $tour->fill([
                'title' => $meta['title'],
                'slug' => $slug,
                'description' => $meta['description'],
                'status' => 'published',
                'placement' => 'homepage',
                'media_asset_id' => $media->id,
                'position' => $position,
                'source_hash' => $sourceHash,
                'seed_key' => 'tour:'.$slug,
                'deleted_at' => null,
            ]);
            $tour->save();

            $audit->record('virtual_tour.seeded', $tour, ['source' => $filename, 'slug' => $slug], $owner->id);
            $position++;
        }

        $this->command?->info("Berhasil melakukan seeding {$position} panorama 360° ke public storage teroptimasi WebP.");
    }

    /**
     * Konversi panorama mentah resolusi besar menjadi WebP 2048x1024 teroptimasi dan WebP thumbnail 480x240.
     */
    protected function optimizeAndStorePanorama(string $sourcePath, string $panoPath, string $thumbPath): void
    {
        $input = @imagecreatefromjpeg($sourcePath);
        if (! $input) {
            $content = file_get_contents($sourcePath);
            $input = @imagecreatefromstring($content);
        }

        if (! $input) {
            return;
        }

        $origW = imagesx($input);
        $origH = imagesy($input);

        // Optimasi Panorama 2:1 Equirectangular (2048x1024 - ukuran ideal untuk WebGL 360 mobile & desktop)
        $targetW = 2048;
        $targetH = 1024;
        $panoCanvas = imagecreatetruecolor($targetW, $targetH);
        imagecopyresampled($panoCanvas, $input, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

        ob_start();
        imagewebp($panoCanvas, null, 78);
        $panoBytes = ob_get_clean();
        imagedestroy($panoCanvas);

        Storage::disk('public')->put($panoPath, $panoBytes);

        // Thumbnail Cepat (480x240 - ~15KB untuk loading instan)
        $thumbW = 480;
        $thumbH = 240;
        $thumbCanvas = imagecreatetruecolor($thumbW, $thumbH);
        imagecopyresampled($thumbCanvas, $input, 0, 0, 0, 0, $thumbW, $thumbH, $origW, $origH);

        ob_start();
        imagewebp($thumbCanvas, null, 72);
        $thumbBytes = ob_get_clean();
        imagedestroy($thumbCanvas);

        Storage::disk('public')->put($thumbPath, $thumbBytes);

        imagedestroy($input);
    }
}
