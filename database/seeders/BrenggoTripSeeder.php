<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AccessService;
use App\Services\PublicContentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrenggoTripSeeder extends Seeder
{
    /** @var array<string, array{source: string, alt: string}> */
    private array $photos = [
        'bromo-01' => ['source' => 'bromo-01.webp', 'alt' => 'Kawah Gunung Bromo dan Gunung Batok dari sisi kaldera.'],
        'bromo-02' => ['source' => 'volcano-02.webp', 'alt' => 'Pemandangan Gunung Bromo dan Lautan Pasir.'],
        'bromo-03' => ['source' => 'volcano-03.webp', 'alt' => 'Matahari terbit di kawasan Bromo Tengger Semeru.'],
        'bromo-04' => ['source' => 'volcano-04.webp', 'alt' => 'Lanskap Gunung Bromo di pagi hari.'],
        'bromo-05' => ['source' => 'volcano-05.webp', 'alt' => 'Jalur perjalanan vulkanik di kawasan Bromo.'],
        'islands-01' => ['source' => 'islands-01.webp', 'alt' => 'Pulau Padar di Taman Nasional Komodo.'],
        'islands-02' => ['source' => 'islands-02.webp', 'alt' => 'Kepulauan di sekitar Labuan Bajo.'],
        'islands-03' => ['source' => 'islands-03.webp', 'alt' => 'Perairan dan pulau-pulau di kawasan Nusa Tenggara.'],
        'islands-04' => ['source' => 'islands-04.webp', 'alt' => 'Pantai dan perairan jernih di kawasan kepulauan Indonesia.'],
        'islands-05' => ['source' => 'islands-05.webp', 'alt' => 'Lanskap pesisir untuk aktivitas jelajah pulau.'],
        'nusantara-01' => ['source' => 'nusantara-01.webp', 'alt' => 'Lanskap perjalanan di Indonesia.'],
        'nusantara-02' => ['source' => 'nusantara-02.webp', 'alt' => 'Pura Ulun Danu Beratan di Bali.'],
        'nusantara-03' => ['source' => 'nusantara-03.webp', 'alt' => 'Suasana wisata alam di Indonesia.'],
        'nusantara-04' => ['source' => 'nusantara-04.webp', 'alt' => 'Lanskap perjalanan nusantara.'],
        'nusantara-05' => ['source' => 'nusantara-05.webp', 'alt' => 'Pemandangan destinasi lokal Indonesia.'],
        'ijen-01' => ['source' => 'kawah-ijen.webp', 'alt' => 'Kawah Ijen, foto Ardhanragil, CC BY-SA 4.0. Sumber: https://commons.wikimedia.org/wiki/File:KAWAH_IJEN.jpg'],
    ];

    public function run(AccessService $access): void
    {
        $vendor = Vendor::where('name', 'BRENGGO.ID')->first();
        $owner = $vendor?->user;

        if (! $owner) {
            $email = config('demo.vendor_email');
            $owner = User::where('email', $email)->first();

            if ($owner && ! $owner->roles()->where('name', 'vendor_admin')->exists()) {
                if ($owner->roles()->exists() || $owner->vendor()->exists()) {
                    throw new \RuntimeException('Email Brenggo sudah dipakai akun yang bukan vendor.');
                }

                $access->grant($owner, 'vendor_admin');
            }

            if (! $owner) {
                $password = Str::password(32);
                $owner = User::create([
                    'name' => 'Vendor Brenggo',
                    'email' => $email,
                    'password' => Hash::make($password),
                    'phone' => '081200000001',
                    'city' => 'Malang',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            }

            $access->grant($owner, 'vendor_admin');
            $vendor ??= Vendor::firstOrCreate(['name' => 'BRENGGO.ID'], [
                'user_id' => $owner->id,
                'city' => 'Malang',
                'email' => $owner->email,
                'phone' => $owner->phone ?: '081200000001',
                'description' => 'Mitra perjalanan yang mengelola paket Bromo dan Kawah Ijen melalui Portal Mitra TapakLokal.',
                'status' => 'verified',
                'verification_note' => 'Akun katalog vendor yang dikelola melalui sistem TapakLokal.',
            ]);
        }

        foreach ($this->tripCatalog() as $item) {
            $images = array_map(fn (string $photo) => $this->storePhoto($vendor, $item['slug'], $photo), $item['photos']);
            $start = today()->addDays($item['days_ahead']);
            $trip = Trip::withTrashed()->firstOrCreate(['slug' => $item['slug']], [
                'vendor_id' => $vendor->id,
                'title' => $item['title'],
                'type' => $item['type'],
                'destination' => $item['destination'],
                'description' => $item['description'],
                'itinerary' => $item['itinerary'],
                'meeting_point' => $item['meeting_point'],
                'image_url' => $images[0],
                'departure_date' => $start,
                'end_date' => $start->copy()->addDays($item['duration_days'] - 1),
                'capacity' => $item['capacity'],
                'reserved_seats' => 0,
                'price' => $item['price'],
                'status' => 'published',
                'experience' => $this->experience($item, $images),
            ]);

            $this->syncSeedExperience($trip, $item, $images);
        }

        $this->updateUntouchedLegacyCovers($vendor);
        $this->enrichLegacyTrips($vendor);

        Cache::forget(PublicContentService::VERSION_KEY);
        $this->command?->info('Vendor Brenggo dan dua paket tripnya tersedia di portal vendor.');
    }

    /** @return array<int, array<string, mixed>> */
    private function tripCatalog(): array
    {
        return [
            [
                'slug' => 'brenggo-bromo-sunrise-open-trip', 'title' => 'Open Trip Bromo Sunrise dari Malang', 'type' => 'open-trip',
                'destination' => 'Gunung Bromo, Probolinggo', 'meeting_point' => 'Stasiun Malang Kota Baru', 'days_ahead' => 14,
                'duration_days' => 1, 'capacity' => 12, 'price' => 575000, 'meeting_time' => '00.30 WIB',
                'description' => 'Perjalanan sehari dari Malang untuk menikmati matahari terbit, Lautan Pasir, dan kawasan kawah Bromo bersama pemandu lokal.',
                'itinerary' => "00.30 Penjemputan di Stasiun Malang Kota Baru\n03.30 Tiba di area Penanjakan\n05.00 Menikmati matahari terbit\n07.00 Menjelajahi Lautan Pasir dan kawah Bromo\n11.00 Perjalanan kembali ke Malang",
                'photos' => ['bromo-01', 'bromo-02', 'bromo-03', 'bromo-04', 'bromo-05'],
                'coordinates' => '-7.9425,112.9530', 'stops' => ['Stasiun Malang Kota Baru', 'Penanjakan', 'Kawah Bromo'],
                'highlights' => [
                    ['title' => 'Matahari terbit Bromo', 'time' => '05.00 WIB', 'location' => 'Penanjakan', 'description' => 'Menikmati panorama kaldera Bromo pada pagi hari.'],
                    ['title' => 'Lautan Pasir dan kawah', 'time' => '07.00 WIB', 'location' => 'Gunung Bromo', 'description' => 'Menjelajahi kawasan Lautan Pasir dan jalur menuju kawah sesuai arahan petugas.'],
                ],
                'days' => [['day' => 'Hari 1', 'meals' => 'Makan pribadi', 'activities' => ['Penjemputan di Malang', 'Melihat matahari terbit di Penanjakan', 'Menjelajahi Lautan Pasir dan kawah Bromo', 'Kembali ke Malang']]],
                'facilities' => [['label' => 'Jeep Bromo', 'icon' => 'Compass'], ['label' => 'Pemandu lokal', 'icon' => 'Users'], ['label' => 'Air mineral', 'icon' => 'Utensils'], ['label' => 'P3K', 'icon' => 'ShieldCheck']],
                'facility_details' => [['title' => 'Transportasi jeep Bromo', 'category' => 'Transportasi', 'note' => 'Transportasi lokal di kawasan Bromo sesuai rute perjalanan.'], ['title' => 'Pemandu lokal', 'category' => 'Layanan', 'note' => 'Pemandu menemani perjalanan dan memberi arahan keselamatan.']],
                'included' => ['Transportasi jeep di Bromo', 'Pemandu lokal', 'Air mineral'], 'excluded' => ['Tiket masuk kawasan', 'Makan pribadi', 'Transportasi menuju Malang'],
                'faqs' => [['question' => 'Di mana titik kumpul perjalanan?', 'answer' => 'Peserta bertemu di Stasiun Malang Kota Baru.'], ['question' => 'Apakah tiket kawasan termasuk?', 'answer' => 'Tiket masuk kawasan dibayar terpisah.']],
            ],
            [
                'slug' => 'brenggo-kawah-ijen-blue-fire-private', 'title' => 'Private Trip Kawah Ijen Blue Fire', 'type' => 'private-trip',
                'destination' => 'Kawah Ijen, Banyuwangi', 'meeting_point' => 'Banyuwangi Kota', 'days_ahead' => 21,
                'duration_days' => 1, 'capacity' => 6, 'price' => 1350000, 'meeting_time' => '00.30 WIB',
                'description' => 'Pendakian malam menuju Kawah Ijen untuk menyaksikan kawah dan fenomena api biru jika kondisi serta peraturan setempat memungkinkan.',
                'itinerary' => "00.30 Penjemputan di Banyuwangi Kota\n02.00 Tiba di Paltuding dan persiapan\n02.30 Pendakian bersama pemandu\n06.00 Menikmati panorama Kawah Ijen\n09.00 Kembali ke Banyuwangi",
                'photos' => ['ijen-01', 'bromo-02', 'bromo-03', 'bromo-04', 'bromo-05'],
                'coordinates' => '-8.0588,114.2424', 'stops' => ['Banyuwangi Kota', 'Pos Paltuding', 'Kawah Ijen'],
                'photo_credit' => 'Foto: Ardhanragil · Wikimedia Commons · CC BY-SA 4.0',
                'highlights' => [['title' => 'Pendakian Kawah Ijen', 'time' => '02.30 WIB', 'location' => 'Pos Paltuding', 'description' => 'Pendakian dengan pemandu lokal; fenomena api biru bergantung pada kondisi dan aturan kawasan.']],
                'days' => [['day' => 'Hari 1', 'meals' => 'Makan pribadi', 'activities' => ['Penjemputan di Banyuwangi', 'Persiapan di Paltuding', 'Pendakian bersama pemandu', 'Kembali ke Banyuwangi']]],
                'facilities' => [['label' => 'Transportasi lokal', 'icon' => 'Compass'], ['label' => 'Pemandu lokal', 'icon' => 'Users'], ['label' => 'Masker gas', 'icon' => 'ShieldCheck'], ['label' => 'Air mineral', 'icon' => 'Utensils']],
                'facility_details' => [['title' => 'Transportasi lokal', 'category' => 'Transportasi', 'note' => 'Perjalanan pulang-pergi Banyuwangi dan Paltuding.'], ['title' => 'Pemandu lokal', 'category' => 'Layanan', 'note' => 'Pemandu mendampingi peserta mengikuti aturan kawasan.']],
                'included' => ['Transportasi lokal', 'Pemandu lokal', 'Masker gas'], 'excluded' => ['Tiket masuk kawasan', 'Makan pribadi', 'Transportasi ke Banyuwangi'],
                'faqs' => [['question' => 'Apakah api biru selalu terlihat?', 'answer' => 'Tidak. Pengamatan bergantung pada kondisi alam dan aturan kawasan saat kunjungan.'], ['question' => 'Apakah pendakian cocok untuk semua orang?', 'answer' => 'Peserta perlu mempertimbangkan kondisi fisik dan mengikuti petunjuk pemandu.']],
            ],
        ];
    }

    /**
     * Keep the legacy demo records usable while their vendor replaces the data
     * through the portal. Their public detail must use the same schema as every
     * currently submitted trip.
     *
     * @return array<int, array<string, mixed>>
     */
    private function legacyTripCatalog(): array
    {
        return [
            [
                'slug' => 'komodo', 'title' => 'Open Trip Pulau Komodo', 'type' => 'open-trip', 'destination' => 'Pulau Komodo, NTT',
                'meeting_point' => 'Pelabuhan Marina Labuan Bajo', 'meeting_time' => '07.00 WITA', 'coordinates' => '-8.4964,119.8877',
                'description' => 'Pelayaran kelompok kecil dari Labuan Bajo menuju Pulau Padar, perairan Komodo, dan titik jelajah pilihan bersama kru lokal.',
                'itinerary' => "07.00 Registrasi di Marina Labuan Bajo\n09.00 Pelayaran menuju Pulau Padar\n13.00 Aktivitas pesisir dan snorkeling sesuai kondisi\n16.00 Kembali ke Labuan Bajo",
                'photos' => ['islands-01', 'islands-02', 'islands-03', 'islands-04', 'islands-05'], 'stops' => ['Marina Labuan Bajo', 'Pulau Padar', 'Pulau Komodo'],
                'days' => [['day' => 'Hari 1', 'meals' => 'Makan siang sesuai paket', 'activities' => ['Registrasi di Marina Labuan Bajo', 'Pelayaran bersama kru lokal', 'Jelajah Pulau Padar dan titik pesisir', 'Kembali ke Labuan Bajo']]],
                'facilities' => [['label' => 'Kapal lokal', 'icon' => 'Compass'], ['label' => 'Pemandu lokal', 'icon' => 'Users'], ['label' => 'Dokumentasi', 'icon' => 'Camera'], ['label' => 'P3K', 'icon' => 'ShieldCheck']],
                'facility_details' => [['title' => 'Kapal lokal', 'category' => 'Transportasi', 'note' => 'Kapal sesuai kapasitas paket dan kondisi perairan.'], ['title' => 'Pemandu lokal', 'category' => 'Layanan', 'note' => 'Memandu rute serta briefing keselamatan perjalanan.'], ['title' => 'P3K kelompok', 'category' => 'Keamanan', 'note' => 'Perlengkapan pertolongan pertama tersedia di kapal.']],
                'included' => ['Kapal lokal sesuai rute', 'Pemandu lokal', 'Dokumentasi kelompok', 'P3K kelompok'], 'excluded' => ['Tiket kawasan', 'Makan di luar paket', 'Pengeluaran pribadi'],
                'packing_items' => ['Pakaian ganti', 'Tabir surya', 'Obat pribadi', 'Tas tahan air', 'Sandal atau sepatu antiselip'],
                'faqs' => [['question' => 'Apakah trip tetap berjalan saat ombak tinggi?', 'answer' => 'Keselamatan menjadi prioritas. Vendor dapat menyesuaikan rute atau jadwal mengikuti arahan otoritas pelabuhan.'], ['question' => 'Di mana titik kumpulnya?', 'answer' => 'Peserta bertemu di Marina Labuan Bajo sesuai jam briefing pada informasi keberangkatan.'], ['question' => 'Apakah alat snorkeling termasuk?', 'answer' => 'Konfirmasi ketersediaan alat kepada vendor sebelum melakukan pemesanan.']],
            ],
            [
                'slug' => 'bali', 'title' => 'Open Trip Bali', 'type' => 'open-trip', 'destination' => 'Bali',
                'meeting_point' => 'Titik kumpul Denpasar', 'meeting_time' => '08.00 WITA', 'coordinates' => '-8.4095,115.1889',
                'description' => 'Perjalanan kelompok kecil untuk menikmati lanskap budaya dan waktu santai di Bali bersama pendamping lokal.',
                'itinerary' => "08.00 Registrasi di Denpasar\n10.00 Kunjungan budaya dan lanskap pilihan\n13.00 Waktu makan siang\n16.00 Dokumentasi dan perjalanan selesai",
                'photos' => ['nusantara-02', 'nusantara-01', 'nusantara-03', 'nusantara-04', 'nusantara-05'], 'stops' => ['Titik kumpul Denpasar', 'Pura Ulun Danu Beratan', 'Area wisata Bali'],
                'days' => [['day' => 'Hari 1', 'meals' => 'Makan siang pribadi', 'activities' => ['Registrasi dan briefing', 'Kunjungan budaya bersama pendamping lokal', 'Waktu bebas untuk dokumentasi', 'Kembali ke titik akhir perjalanan']]],
                'facilities' => [['label' => 'Pendamping lokal', 'icon' => 'Users'], ['label' => 'Transportasi rute', 'icon' => 'Compass'], ['label' => 'Dokumentasi', 'icon' => 'Camera'], ['label' => 'P3K', 'icon' => 'ShieldCheck']],
                'facility_details' => [['title' => 'Pendamping lokal', 'category' => 'Layanan', 'note' => 'Mendampingi alur kunjungan dan kebutuhan informasi dasar.'], ['title' => 'Transportasi rute', 'category' => 'Transportasi', 'note' => 'Rute mengikuti jadwal paket dan kondisi lalu lintas.'], ['title' => 'P3K kelompok', 'category' => 'Keamanan', 'note' => 'Tersedia untuk kebutuhan pertolongan pertama.']],
                'included' => ['Pendamping lokal', 'Transportasi sesuai rute', 'Dokumentasi kelompok', 'P3K kelompok'], 'excluded' => ['Tiket masuk lokasi', 'Makan pribadi', 'Pengeluaran pribadi'],
                'packing_items' => ['Pakaian nyaman', 'Tabir surya', 'Botol minum', 'Obat pribadi', 'Alas kaki nyaman'],
                'faqs' => [['question' => 'Apakah jadwal kunjungan bisa berubah?', 'answer' => 'Ya. Vendor dapat menyesuaikan urutan kunjungan untuk kondisi lapangan dan kelancaran perjalanan.'], ['question' => 'Apa yang perlu dibawa?', 'answer' => 'Bawa pakaian nyaman, tabir surya, air minum, dan obat pribadi bila diperlukan.'], ['question' => 'Kapan detail akhir dikirim?', 'answer' => 'Vendor mengirimkan informasi keberangkatan setelah pesanan terkonfirmasi.']],
            ],
            [
                'slug' => 'labuan-bajo', 'title' => 'Private Trip Labuan Bajo', 'type' => 'private-trip', 'destination' => 'Labuan Bajo, NTT',
                'meeting_point' => 'Bandara Komodo atau Marina Labuan Bajo', 'meeting_time' => '08.00 WITA', 'coordinates' => '-8.4964,119.8877',
                'description' => 'Perjalanan privat Labuan Bajo untuk rombongan dengan agenda pelayaran dan jelajah pesisir yang dapat dikoordinasikan bersama vendor.',
                'itinerary' => "08.00 Penjemputan sesuai titik yang disepakati\n10.00 Pelayaran dan jelajah pulau\n13.00 Waktu makan siang\n16.00 Kembali ke Labuan Bajo",
                'photos' => ['islands-02', 'islands-01', 'islands-03', 'islands-04', 'islands-05'], 'stops' => ['Marina Labuan Bajo', 'Pulau Padar', 'Pesisir Komodo'],
                'days' => [['day' => 'Hari 1', 'meals' => 'Makan sesuai kesepakatan', 'activities' => ['Penjemputan atau registrasi rombongan', 'Briefing rute bersama kru', 'Pelayaran dan aktivitas pesisir', 'Kembali ke Labuan Bajo']]],
                'facilities' => [['label' => 'Kapal privat', 'icon' => 'Compass'], ['label' => 'Pemandu lokal', 'icon' => 'Users'], ['label' => 'Dokumentasi', 'icon' => 'Camera'], ['label' => 'P3K', 'icon' => 'ShieldCheck']],
                'facility_details' => [['title' => 'Kapal privat', 'category' => 'Transportasi', 'note' => 'Jenis kapal dan rute dikonfirmasi bersama vendor.'], ['title' => 'Pemandu lokal', 'category' => 'Layanan', 'note' => 'Mendampingi rombongan sepanjang agenda perjalanan.'], ['title' => 'P3K kelompok', 'category' => 'Keamanan', 'note' => 'Tersedia untuk kebutuhan pertolongan pertama.']],
                'included' => ['Kapal sesuai kesepakatan', 'Pemandu lokal', 'Dokumentasi kelompok', 'P3K kelompok'], 'excluded' => ['Tiket kawasan', 'Makan di luar kesepakatan', 'Pengeluaran pribadi'],
                'packing_items' => ['Pakaian ganti', 'Tabir surya', 'Tas tahan air', 'Obat pribadi', 'Alas kaki antiselip'],
                'faqs' => [['question' => 'Bisakah rute disesuaikan?', 'answer' => 'Bisa. Diskusikan kebutuhan rombongan dan ketersediaan rute dengan vendor sebelum memesan.'], ['question' => 'Apakah penjemputan tersedia?', 'answer' => 'Titik penjemputan dapat dikoordinasikan bersama vendor setelah pemesanan.'], ['question' => 'Apakah trip aman untuk anak?', 'answer' => 'Sampaikan usia peserta kepada vendor agar perlengkapan dan rute dapat disesuaikan.']],
            ],
        ];
    }

    /** @param array<string, mixed> $item
     * @param  array<int, string>  $images
     * @return array<string, mixed>
     */
    private function experience(array $item, array $images): array
    {
        $stops = $item['stops'];
        $fallbackHighlights = [
            ['title' => 'Briefing dan persiapan', 'time' => 'Hari 1 · '.$item['meeting_time'], 'location' => $stops[0], 'description' => 'Registrasi, pengecekan perlengkapan, dan penjelasan alur perjalanan.', 'icon' => 'ShieldCheck'],
            ['title' => $stops[1], 'time' => 'Hari 1 · 10.00', 'location' => $item['destination'], 'description' => 'Aktivitas utama bersama pemandu lokal sesuai kondisi perjalanan.', 'icon' => 'Compass'],
            ['title' => 'Dokumentasi perjalanan', 'time' => 'Hari 1 · 15.00', 'location' => $stops[2], 'description' => 'Waktu menikmati lokasi dan dokumentasi kelompok sebelum pulang.', 'icon' => 'Camera'],
        ];
        $highlights = collect(array_slice([...($item['highlights'] ?? []), ...$fallbackHighlights], 0, 3))->values()->map(function (array $highlight, int $index) use ($images): array {
            $highlight['icon'] ??= ['ShieldCheck', 'Compass', 'Camera'][$index] ?? 'Compass';
            $highlight['images'] = [$images[$index], $images[($index + 1) % count($images)]];

            return $highlight;
        })->all();
        $facilityDetails = array_slice([...$item['facility_details'], ['title' => 'P3K kelompok', 'category' => 'Keamanan', 'note' => 'Tersedia untuk kebutuhan pertolongan pertama selama perjalanan.']], 0, 3);
        $faqs = array_slice([...$item['faqs'], ['question' => 'Kapan detail keberangkatan dikonfirmasi?', 'answer' => 'Rincian titik kumpul dan persiapan akhir dikirimkan vendor setelah pesanan terkonfirmasi.']], 0, 3);

        return [
            'catalog_schema_version' => 4,
            'detail' => [
                'subtitle' => $item['description'], 'images' => $images, 'coordinates' => $item['coordinates'], 'meetingTime' => $item['meeting_time'],
                'arrivalNote' => 'Hadir 30 menit sebelum briefing dan siapkan perlengkapan pribadi.',
                'meetingNote' => 'Temui pemandu di '.$item['meeting_point'].' dan tunjukkan bukti pemesanan.',
            ],
            'description_html' => '<h3>'.e($item['title']).'</h3><p>'.e($item['description']).'</p><p>Rincian perjalanan dikelola langsung oleh vendor melalui Portal Mitra TapakLokal.</p>',
            'itinerary_html' => '<h3>Rencana perjalanan</h3><p>Urutan kegiatan mengikuti kondisi lapangan, keselamatan peserta, dan arahan pemandu lokal.</p>',
            'highlights' => $highlights,
            'destinations' => [
                ['name' => $stops[0], 'subtitle' => 'Titik awal perjalanan', 'time' => $item['meeting_time'], 'activity' => 'Registrasi dan briefing', 'note' => 'Siapkan identitas, perlengkapan pribadi, dan bukti pemesanan.', 'coordinates' => $item['coordinates'], 'image_url' => $images[0]],
                ['name' => $stops[1], 'subtitle' => 'Titik utama perjalanan', 'time' => '10.00', 'activity' => 'Aktivitas utama bersama pemandu', 'note' => 'Tetap bersama rombongan dan ikuti arahan pemandu.', 'coordinates' => $item['coordinates'], 'image_url' => $images[2]],
                ['name' => $stops[2], 'subtitle' => 'Penutup perjalanan', 'time' => '15.00', 'activity' => 'Istirahat dan dokumentasi', 'note' => 'Waktu dapat berubah mengikuti kondisi lapangan.', 'coordinates' => $item['coordinates'], 'image_url' => $images[4]],
            ],
            'itineraryDays' => $item['days'],
            'facilities' => $item['facilities'],
            'facilityDetails' => $facilityDetails,
            'included' => $item['included'],
            'excluded' => $item['excluded'],
            'packingItems' => $item['packing_items'] ?? ['Pakaian nyaman', 'Air minum', 'Obat pribadi', 'Alas kaki sesuai aktivitas', 'Baju ganti'],
            'faqs' => $faqs,
            'panoramas' => [],
            ...(isset($item['photo_credit']) ? ['photo_credit' => $item['photo_credit']] : []),
        ];
    }

    /** @param array<string, mixed> $item
     * @param  array<int, string>  $images
     */
    private function syncSeedExperience(Trip $trip, array $item, array $images): void
    {
        if ($trip->trashed() || ($trip->experience['catalog_schema_version'] ?? 0) >= 4) {
            return;
        }

        $trip->forceFill(['experience' => $this->experience($item, $images)])->save();
    }

    private function enrichLegacyTrips(Vendor $vendor): void
    {
        foreach ($this->legacyTripCatalog() as $item) {
            $trip = Trip::query()->whereBelongsTo($vendor)->where('seed_key', 'trip:'.$item['slug'])->first();
            if (! $trip) {
                continue;
            }

            $images = array_map(fn (string $photo) => $this->storePhoto($vendor, $item['slug'], $photo), $item['photos']);
            $this->syncSeedExperience($trip, $item, $images);
        }
    }

    private function storePhoto(Vendor $vendor, string $tripSlug, string $key): string
    {
        $photo = $this->photos[$key];
        $sourcePath = public_path('Assets/Images/trips/'.$photo['source']);
        $disk = Storage::disk('public');
        $path = 'trips/catalog/brenggo-'.$tripSlug.'-'.$key.'.webp';

        if (! $disk->exists($path)) {
            if (! is_file($sourcePath)) {
                throw new \RuntimeException('Foto untuk destinasi trip Brenggo tidak ditemukan: '.$photo['source']);
            }

            $image = imagecreatefromstring(file_get_contents($sourcePath));
            if (! $image) {
                throw new \RuntimeException('Foto trip Brenggo gagal dibaca: '.$photo['source']);
            }

            $width = min(1600, imagesx($image));
            $output = imagesx($image) > $width ? imagescale($image, $width, (int) round(imagesy($image) * ($width / imagesx($image))), IMG_BICUBIC) : $image;
            $stream = fopen('php://temp', 'w+b');
            try {
                if (! $output || ! imagewebp($output, $stream, 82)) {
                    throw new \RuntimeException('Foto trip Brenggo gagal dioptimalkan: '.$photo['source']);
                }

                rewind($stream);
                if (! $disk->put($path, $stream)) {
                    throw new \RuntimeException('Foto trip Brenggo gagal disimpan: '.$photo['source']);
                }
            } finally {
                fclose($stream);
                if ($output !== $image) {
                    imagedestroy($output);
                }
                imagedestroy($image);
            }
        }

        MediaAsset::firstOrCreate(['disk' => 'public', 'path' => $path], [
            'user_id' => $vendor->user_id,
            'name' => basename($path),
            'mime_type' => 'image/webp',
            'size' => $disk->size($path),
            'alt_text' => $photo['alt'],
            'visibility' => 'public',
        ]);

        return url($disk->url($path));
    }

    private function updateUntouchedLegacyCovers(Vendor $vendor): void
    {
        $source = json_decode(file_get_contents(__DIR__.'/data/website-content.json'), true, 512, JSON_THROW_ON_ERROR);
        $legacyCovers = [
            'komodo' => ['photo' => 'islands-01', 'image' => collect($source['trips'])->firstWhere('id', 'komodo')['image']],
            'bali' => ['photo' => 'nusantara-02', 'image' => collect($source['trips'])->firstWhere('id', 'bali')['image']],
            'labuan-bajo' => ['photo' => 'islands-02', 'image' => $source['details']['private-trip']['detail']['images'][0]],
        ];

        foreach ($legacyCovers as $slug => $cover) {
            $trip = Trip::query()->whereBelongsTo($vendor)
                ->where('seed_key', 'trip:'.$slug)
                ->where('image_url', $cover['image'])
                ->first();

            if (! $trip) {
                continue;
            }

            $trip->forceFill(['image_url' => $this->storePhoto($vendor, $slug, $cover['photo'])])->save();
        }
    }
}
