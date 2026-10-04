<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Models\User;
use App\Models\VirtualTour;
use App\Services\AuditService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class VirtualTourSeeder extends Seeder
{
    /**
     * Data konfigurasi panorama 360°.
     *
     * @var array<string, array{title: string, slug: string, description: string, alt: string}>
     */
    protected array $catalog = [
        'curug' => [
            'title' => 'Kawasan Curug Alami',
            'slug' => 'kawasan-curug-alami',
            'description' => 'Panorama 360° pemandangan alami air terjun jernih dengan suasana hutan pegunungan yang asri dan sejuk di Jawa Barat.',
            'alt' => 'Panorama 360 derajat suasana kawasan wisata air terjun alami dan pepohonan hijau rindang',
        ],
        'curung cinangka' => [
            'title' => 'Curug Cinangka',
            'slug' => 'curug-cinangka',
            'description' => 'Eksplorasi virtual 360° Curug Cinangka yang menampilkan keindahan tebing batuan alami dan aliran air terjun tersembunyi yang eksotis.',
            'alt' => 'Panorama 360 derajat tebing batu dan aliran air Curug Cinangka yang eksotis',
        ],
        'curung-cidaun' => [
            'title' => 'Curug Cidaun',
            'slug' => 'curug-cidaun',
            'description' => 'Tampilan interaktif 360° Curug Cidaun dengan panorama air terjun bertingkat yang menyejukkan di tengah lanskap alam terbuka.',
            'alt' => 'Panorama 360 derajat pemandangan air terjun bertingkat Curug Cidaun',
        ],
        'mt-gede-basecamp' => [
            'title' => 'Basecamp Gunung Gede',
            'slug' => 'basecamp-gunung-gede',
            'description' => 'Sudut pandang 360° area basecamp pendakian Gunung Gede Pangrango, tempat istirahat dan registrasi para pendaki sebelum menuju puncak.',
            'alt' => 'Panorama 360 derajat area basecamp pendakian Gunung Gede Pangrango',
        ],
        'mt-gede-parkiran-atas' => [
            'title' => 'Area Parkir Atas Gunung Gede',
            'slug' => 'parkiran-atas-gunung-gede',
            'description' => 'Panorama 360° area parkir atas jalur pendakian Gunung Gede dengan latar pemandangan bukit dan akses langsung ke pos gerbang.',
            'alt' => 'Panorama 360 derajat area parkir atas jalur pendakian Gunung Gede dengan lanskap perbukitan',
        ],
        'mt-gede-parkiran-basecamp' => [
            'title' => 'Parkiran Basecamp Gunung Gede',
            'slug' => 'parkiran-basecamp-gunung-gede',
            'description' => 'Tinjauan 360° fasilitas parkir kendaraan di sekitar basecamp Gunung Gede yang luas, aman, dan mudah diakses pengunjung.',
            'alt' => 'Panorama 360 derajat fasilitas parkir kendaraan di sekitar basecamp Gunung Gede',
        ],
        'mt-gede-parkiran-bawah' => [
            'title' => 'Area Parkir Bawah Gunung Gede',
            'slug' => 'parkiran-bawah-gunung-gede',
            'description' => 'Eksplorasi 360° area parkir bawah Gunung Gede sebagai titik temu awal kedatangan rombongan pendaki dan wisatawan lokal.',
            'alt' => 'Panorama 360 derajat area parkir bawah sebagai titik temu kedatangan pendaki Gunung Gede',
        ],
        'mt-gede-pintu rimba&regis' => [
            'title' => 'Pintu Rimba & Pos Registrasi Gede',
            'slug' => 'pintu-rimba-pos-registrasi-gede',
            'description' => 'Panorama 360° gerbang pintu rimba dan pos pemeriksaan resmi Taman Nasional Gunung Gede Pangrango sebelum memasuki hutan lindung.',
            'alt' => 'Panorama 360 derajat gerbang pintu rimba dan pos pemeriksaan Taman Nasional Gunung Gede Pangrango',
        ],
        'mt-salak-parkiran' => [
            'title' => 'Area Parkir Jalur Salak',
            'slug' => 'area-parkir-jalur-salak',
            'description' => 'Panorama 360° area parkir kedatangan pendakian Gunung Salak, dikelilingi rindangnya pepohonan pinus dan udara sejuk pegunungan.',
            'alt' => 'Panorama 360 derajat area parkir kedatangan pendakian Gunung Salak di bawah pohon pinus',
        ],
        'mt-salak-pintu-rimba-ceksampah' => [
            'title' => 'Pintu Rimba & Pos Sampah Salak',
            'slug' => 'pintu-rimba-pos-sampah-salak',
            'description' => 'Tinjauan 360° titik pintu rimba jalur pendakian Salak serta pos edukasi kelestarian lingkungan untuk pemeriksaan sampah pendaki.',
            'alt' => 'Panorama 360 derajat pintu rimba jalur pendakian Gunung Salak dan pos kelestarian lingkungan',
        ],
        'mt-salak-regist-toilet' => [
            'title' => 'Pos Registrasi & Fasilitas Salak',
            'slug' => 'pos-registrasi-fasilitas-salak',
            'description' => 'Suasana 360° pos registrasi, loket simaksi, dan fasilitas umum di titik awal jalur pendakian Gunung Salak.',
            'alt' => 'Panorama 360 derajat pos registrasi simaksi dan fasilitas umum di titik awal jalur Gunung Salak',
        ],
        'parkiran-curug' => [
            'title' => 'Area Parkir Wisata Curug',
            'slug' => 'area-parkir-wisata-curug',
            'description' => 'Panorama 360° area parkir dan gerbang masuk kawasan wisata air terjun dengan akses jalan yang mudah dan tertata rapi.',
            'alt' => 'Panorama 360 derajat area parkir dan pintu masuk kawasan wisata air terjun',
        ],
    ];

    public function run(?AuditService $audit = null): void
    {
        $audit ??= app(AuditService::class);

        // Ambil user yang ada atau buat akun admin standar jika belum ada
        $owner = User::first() ?? User::create([
            'name' => 'Admin TapakLokal',
            'email' => 'admin@tapaklokal.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $sourceDir = public_path('Assets/Images/360');

        // Pastikan direktori storage public untuk panoramas dan thumbnails sudah ada
        Storage::disk('public')->makeDirectory('panoramas/thumbnails');

        $position = 0;
        foreach ($this->catalog as $baseName => $meta) {
            $slug = $meta['slug'];
            $publicPanoRelPath = 'panoramas/'.$slug.'.webp';
            $publicThumbRelPath = 'panoramas/thumbnails/'.$slug.'.webp';

            // Cari file sumber: utamakan .webp, jika tidak ada cari .jpg
            $sourcePath = null;
            if (is_dir($sourceDir)) {
                $candidates = [
                    $sourceDir.DIRECTORY_SEPARATOR.$baseName.'.webp',
                    $sourceDir.DIRECTORY_SEPARATOR.$baseName.'.jpg',
                    $sourceDir.DIRECTORY_SEPARATOR.$baseName.'.jpeg',
                    $sourceDir.DIRECTORY_SEPARATOR.$baseName.'.png',
                ];
                foreach ($candidates as $cand) {
                    if (is_file($cand)) {
                        $sourcePath = $cand;
                        break;
                    }
                }
            }

            // Jika ada file sumber, pastikan tersalin ke storage public
            if ($sourcePath && file_exists($sourcePath)) {
                if (! Storage::disk('public')->exists($publicPanoRelPath)) {
                    $this->storeOrOptimize($sourcePath, $publicPanoRelPath, $publicThumbRelPath);
                } elseif (! Storage::disk('public')->exists($publicThumbRelPath)) {
                    Storage::disk('public')->put($publicThumbRelPath, file_get_contents($sourcePath));
                }
            }

            $fileSize = Storage::disk('public')->exists($publicPanoRelPath)
                ? Storage::disk('public')->size($publicPanoRelPath)
                : 102400;

            // 1. Simpan / perbarui MediaAsset
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

            // 2. Simpan / perbarui VirtualTour
            $tour = VirtualTour::withTrashed()->where('slug', $slug)->first() ?? new VirtualTour;
            $tour->fill([
                'title' => $meta['title'],
                'slug' => $slug,
                'description' => $meta['description'],
                'status' => 'published',
                'placement' => 'homepage',
                'media_asset_id' => $media->id,
                'position' => $position,
                'source_hash' => $slug,
                'seed_key' => 'tour:'.$slug,
                'deleted_at' => null,
            ]);
            $tour->save();

            try {
                $audit->record('virtual_tour.seeded', $tour, ['slug' => $slug], $owner->id);
            } catch (\Throwable) {
                // Abaikan error audit agar seeder tetap jalan mulus
            }

            $position++;
        }

        $this->command?->info("Berhasil seeding {$position} panorama 360° ke database dan storage.");
    }

    /**
     * Salin file ke storage public (menggunakan GD WebP jika tersedia, atau salin langsung file byte-nya).
     */
    protected function storeOrOptimize(string $sourcePath, string $panoPath, string $thumbPath): void
    {
        $content = file_get_contents($sourcePath);

        // Jika ekstensi GD dan imagewebp tersedia, coba optimasi
        if (function_exists('imagecreatefromstring') && function_exists('imagewebp')) {
            $input = @imagecreatefromstring($content);
            if ($input) {
                $origW = imagesx($input);
                $origH = imagesy($input);

                // Panorama 2048x1024
                $targetW = min(2048, $origW);
                $targetH = (int) round($targetW / 2);
                $panoCanvas = imagecreatetruecolor($targetW, $targetH);
                imagecopyresampled($panoCanvas, $input, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

                ob_start();
                imagewebp($panoCanvas, null, 78);
                $panoBytes = ob_get_clean();
                imagedestroy($panoCanvas);

                if ($panoBytes) {
                    Storage::disk('public')->put($panoPath, $panoBytes);
                }

                // Thumbnail 480x240
                $thumbW = 480;
                $thumbH = 240;
                $thumbCanvas = imagecreatetruecolor($thumbW, $thumbH);
                imagecopyresampled($thumbCanvas, $input, 0, 0, 0, 0, $thumbW, $thumbH, $origW, $origH);

                ob_start();
                imagewebp($thumbCanvas, null, 72);
                $thumbBytes = ob_get_clean();
                imagedestroy($thumbCanvas);

                if ($thumbBytes) {
                    Storage::disk('public')->put($thumbPath, $thumbBytes);
                }

                imagedestroy($input);

                return;
            }
        }

        // Fallback langsung: tulis file bytes tanpa perlu PHP GD
        Storage::disk('public')->put($panoPath, $content);
        Storage::disk('public')->put($thumbPath, $content);
    }
}
