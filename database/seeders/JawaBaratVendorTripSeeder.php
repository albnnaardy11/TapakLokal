<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\MediaAsset;
use App\Models\Review;
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

class JawaBaratVendorTripSeeder extends Seeder
{
    /** @var array<string, array{directory: string, files: array<int, string>}> */
    private array $photoGroups = [
        'salak' => ['directory' => 'Assets/Images/360', 'files' => ['mt-salak-parkiran.jpg', 'mt-salak-regist-toilet.jpg', 'mt-salak-pintu-rimba-ceksampah.jpg', 'parkiran-curug.jpg', 'curug.jpg']],
        'gede' => ['directory' => 'Assets/Images/360', 'files' => ['mt-gede-basecamp.jpg', 'mt-gede-parkiran-atas.jpg', 'mt-gede-parkiran-basecamp.jpg', 'mt-gede-parkiran-bawah.jpg', 'mt-gede-pintu rimba&regis.jpg']],
        'volcano' => ['directory' => 'Assets/Images/trips', 'files' => ['bromo-01.webp', 'volcano-02.webp', 'volcano-03.webp', 'volcano-04.webp', 'volcano-05.webp']],
        'islands' => ['directory' => 'Assets/Images/trips', 'files' => ['islands-01.webp', 'islands-02.webp', 'islands-03.webp', 'islands-04.webp', 'islands-05.webp']],
        'nusantara' => ['directory' => 'Assets/Images/trips', 'files' => ['nusantara-01.webp', 'nusantara-02.webp', 'nusantara-03.webp', 'nusantara-04.webp', 'nusantara-05.webp']],
    ];

    /**
     * The first two vendor accounts are the featured, rated Gunung Salak and
     * Gunung Gede partners. Each subsequent account owns two distinct packages.
     *
     * @var array<int, array{name: string, city: string, slug: string, favorite: bool, trips: array<int, array<string, mixed>>}>
     */
    private array $vendors = [
        ['name' => 'Salak Rimba Adventure', 'city' => 'Bogor', 'slug' => 'salak-rimba', 'favorite' => true, 'trips' => [
            ['title' => 'Open Trip Gunung Salak & Curug', 'slug' => 'salak-rimba-open-trip', 'type' => 'open-trip', 'destination' => 'Gunung Salak, Bogor', 'meeting_point' => 'Pos Registrasi Gunung Salak', 'group' => 'salak', 'price' => 325000, 'days' => 2, 'capacity' => 18, 'coordinates' => '-6.6620,106.7000', 'overview' => 'Dua hari menyusuri jalur Gunung Salak dan area curug bersama pemandu lokal.', 'stops' => ['Pos Registrasi Gunung Salak', 'Jalur Hutan Salak', 'Area Curug']],
            ['title' => 'Private Trip Gunung Salak untuk Rombongan', 'slug' => 'salak-rimba-private-trip', 'type' => 'private-trip', 'destination' => 'Gunung Salak, Bogor', 'meeting_point' => 'Gerbang Utama Pos Salak', 'group' => 'salak', 'price' => 1650000, 'days' => 2, 'capacity' => 10, 'coordinates' => '-6.6620,106.7000', 'overview' => 'Perjalanan privat Gunung Salak yang dapat dikoordinasikan bersama vendor.', 'stops' => ['Gerbang Utama Pos Salak', 'Jalur Hutan Salak', 'Area Curug']],
        ]],
        ['name' => 'Gede Pangrango Explorer', 'city' => 'Cianjur', 'slug' => 'gede-explorer', 'favorite' => true, 'trips' => [
            ['title' => 'Open Trip Gunung Gede Pangrango', 'slug' => 'gede-explorer-open-trip', 'type' => 'open-trip', 'destination' => 'Gunung Gede Pangrango, Cianjur', 'meeting_point' => 'Basecamp Gunung Gede', 'group' => 'gede', 'price' => 475000, 'days' => 2, 'capacity' => 15, 'coordinates' => '-6.7307,106.9846', 'overview' => 'Open trip pendakian Gunung Gede Pangrango dengan briefing dan pemandu lokal.', 'stops' => ['Basecamp Gunung Gede', 'Jalur Pendakian', 'Area Panorama']],
            ['title' => 'Private Trip Gunung Gede Pangrango', 'slug' => 'gede-explorer-private-trip', 'type' => 'private-trip', 'destination' => 'Gunung Gede Pangrango, Cianjur', 'meeting_point' => 'Basecamp Gunung Gede', 'group' => 'gede', 'price' => 2450000, 'days' => 3, 'capacity' => 8, 'coordinates' => '-6.7307,106.9846', 'overview' => 'Pendakian privat Gunung Gede Pangrango untuk rombongan kecil.', 'stops' => ['Basecamp Gunung Gede', 'Jalur Pendakian', 'Area Panorama']],
        ]],
        ['name' => 'Puncak Hijau Trip', 'city' => 'Malang', 'slug' => 'puncak-hijau', 'favorite' => false, 'trips' => [
            ['title' => 'Open Trip Bromo Titik Kumpul Malang 3H2M', 'slug' => 'bromo', 'type' => 'open-trip', 'destination' => 'Gunung Bromo, Probolinggo & Malang', 'meeting_point' => 'Stasiun Malang Kota Baru', 'group' => 'volcano', 'price' => 385000, 'days' => 3, 'capacity' => 20, 'coordinates' => '-7.9425,112.9530', 'overview' => 'Paket Bromo tiga hari dua malam dari Malang untuk jalur vulkanik dan panorama matahari terbit.', 'stops' => ['Stasiun Malang Kota Baru', 'Laut Pasir Bromo', 'Penanjakan']],
            ['title' => 'Private Trip Kawah Ijen Blue Fire', 'slug' => 'ijen-blue-fire-private', 'type' => 'private-trip', 'destination' => 'Kawah Ijen, Banyuwangi', 'meeting_point' => 'Stasiun Banyuwangi Kota', 'group' => 'volcano', 'price' => 1450000, 'days' => 2, 'capacity' => 8, 'coordinates' => '-8.0588,114.2424', 'overview' => 'Perjalanan privat Kawah Ijen dengan waktu keberangkatan yang dikoordinasikan vendor.', 'stops' => ['Stasiun Banyuwangi Kota', 'Pos Paltuding', 'Kawah Ijen']],
        ]],
        ['name' => 'Sunda Trekking Club', 'city' => 'Labuan Bajo', 'slug' => 'sunda-trekking', 'favorite' => false, 'trips' => [
            ['title' => 'Open Trip Labuan Bajo & Pulau Komodo', 'slug' => 'komodo-phinisi', 'type' => 'open-trip', 'destination' => 'Labuan Bajo & Pulau Komodo, NTT', 'meeting_point' => 'Pelabuhan Marina Labuan Bajo', 'group' => 'islands', 'price' => 2450000, 'days' => 3, 'capacity' => 16, 'coordinates' => '-8.4964,119.8877', 'overview' => 'Pelayaran open trip dari Labuan Bajo ke pulau-pulau Komodo bersama operator lokal.', 'stops' => ['Pelabuhan Marina Labuan Bajo', 'Pulau Padar', 'Taman Nasional Komodo']],
            ['title' => 'Private Trip Nusa Penida Selatan', 'slug' => 'nusa-penida-private', 'type' => 'private-trip', 'destination' => 'Nusa Penida, Bali', 'meeting_point' => 'Pelabuhan Sanur', 'group' => 'islands', 'price' => 1200000, 'days' => 2, 'capacity' => 8, 'coordinates' => '-8.7278,115.5444', 'overview' => 'Paket privat Nusa Penida untuk rombongan dengan waktu kunjungan yang dapat disesuaikan.', 'stops' => ['Pelabuhan Sanur', 'Pantai Kelingking', 'Crystal Bay']],
        ]],
        ['name' => 'Bogor Nature Walk', 'city' => 'Jakarta', 'slug' => 'bogor-nature', 'favorite' => false, 'trips' => [
            ['title' => 'Open Trip Pulau Pramuka Kepulauan Seribu', 'slug' => 'pulau-pramuka', 'type' => 'open-trip', 'destination' => 'Pulau Pramuka, Kepulauan Seribu', 'meeting_point' => 'Dermaga Kali Adem', 'group' => 'islands', 'price' => 380000, 'days' => 2, 'capacity' => 24, 'coordinates' => '-5.7444,106.6131', 'overview' => 'Liburan pulau bersama kelompok kecil dari penyeberangan hingga aktivitas pesisir.', 'stops' => ['Dermaga Kali Adem', 'Pulau Pramuka', 'Area Snorkeling']],
            ['title' => 'Private Trip Karimunjawa', 'slug' => 'karimunjawa-private', 'type' => 'private-trip', 'destination' => 'Karimunjawa, Jepara', 'meeting_point' => 'Pelabuhan Kartini Jepara', 'group' => 'islands', 'price' => 1450000, 'days' => 3, 'capacity' => 10, 'coordinates' => '-5.8830,110.4297', 'overview' => 'Paket privat Karimunjawa untuk rombongan yang ingin mengatur waktu jelajah pulau.', 'stops' => ['Pelabuhan Kartini Jepara', 'Pulau Menjangan Kecil', 'Pantai Karimunjawa']],
        ]],
        ['name' => 'Cianjur Mountain Guide', 'city' => 'Wonosobo', 'slug' => 'cianjur-guide', 'favorite' => false, 'trips' => [
            ['title' => 'Open Trip Dieng & Sunrise Sikunir', 'slug' => 'dieng-sikunir', 'type' => 'open-trip', 'destination' => 'Dieng, Wonosobo', 'meeting_point' => 'Terminal Mendolo Wonosobo', 'group' => 'volcano', 'price' => 620000, 'days' => 2, 'capacity' => 18, 'coordinates' => '-7.2081,109.9124', 'overview' => 'Perjalanan Dieng untuk menikmati dataran tinggi, telaga, dan waktu terbaik menuju Sikunir.', 'stops' => ['Terminal Mendolo Wonosobo', 'Dataran Tinggi Dieng', 'Bukit Sikunir']],
            ['title' => 'Private Trip Bandung Selatan & Ciwidey', 'slug' => 'bandung-ciwidey-private', 'type' => 'private-trip', 'destination' => 'Ciwidey, Bandung', 'meeting_point' => 'Stasiun Bandung', 'group' => 'volcano', 'price' => 900000, 'days' => 2, 'capacity' => 8, 'coordinates' => '-7.1670,107.4026', 'overview' => 'Jelajah privat Bandung Selatan dan Ciwidey bersama pemandu lokal.', 'stops' => ['Stasiun Bandung', 'Kawah Putih', 'Ranca Upas']],
        ]],
        ['name' => 'Jalur Hutan Nusantara', 'city' => 'Sorong', 'slug' => 'jalur-hutan-nusantara', 'favorite' => false, 'trips' => [
            ['title' => 'Open Trip Raja Ampat Bahari', 'slug' => 'raja-ampat', 'type' => 'open-trip', 'destination' => 'Raja Ampat, Papua Barat Daya', 'meeting_point' => 'Pelabuhan Sorong', 'group' => 'islands', 'price' => 4950000, 'days' => 4, 'capacity' => 12, 'coordinates' => '-0.7893,130.6773', 'overview' => 'Pelayaran Raja Ampat untuk kelompok kecil dengan rute dan aktivitas yang dikelola tim lokal.', 'stops' => ['Pelabuhan Sorong', 'Wayag', 'Piaynemo']],
            ['title' => 'Private Trip Sumba Timur', 'slug' => 'sumba-private', 'type' => 'private-trip', 'destination' => 'Sumba Timur, NTT', 'meeting_point' => 'Bandara Umbu Mehang Kunda', 'group' => 'nusantara', 'price' => 3250000, 'days' => 4, 'capacity' => 8, 'coordinates' => '-9.6500,120.2640', 'overview' => 'Paket privat Sumba Timur untuk lanskap dan kampung lokal sesuai kebutuhan rombongan.', 'stops' => ['Bandara Umbu Mehang Kunda', 'Bukit Wairinding', 'Pantai Walakiri']],
        ]],
        ['name' => 'Rimba Nusantara Tour', 'city' => 'Bandar Lampung', 'slug' => 'rimba-nusantara', 'favorite' => false, 'trips' => [
            ['title' => 'Open Trip Pulau Pahawang', 'slug' => 'pahawang', 'type' => 'open-trip', 'destination' => 'Pulau Pahawang, Lampung', 'meeting_point' => 'Dermaga Ketapang', 'group' => 'islands', 'price' => 580000, 'days' => 3, 'capacity' => 20, 'coordinates' => '-5.5887,105.2392', 'overview' => 'Open trip pesisir Pahawang dengan penyeberangan, aktivitas laut, dan dokumentasi kelompok.', 'stops' => ['Dermaga Ketapang', 'Pulau Pahawang', 'Taman Karang']],
            ['title' => 'Private Trip Belitung Island Hopping', 'slug' => 'belitung-private', 'type' => 'private-trip', 'destination' => 'Belitung, Kepulauan Bangka Belitung', 'meeting_point' => 'Bandara H.A.S. Hanandjoeddin', 'group' => 'islands', 'price' => 1650000, 'days' => 3, 'capacity' => 8, 'coordinates' => '-2.7457,107.7540', 'overview' => 'Island hopping Belitung untuk rombongan privat dengan pilihan ritme perjalanan dari vendor.', 'stops' => ['Bandara H.A.S. Hanandjoeddin', 'Pantai Tanjung Tinggi', 'Pulau Lengkuas']],
        ]],
        ['name' => 'Lembah Salak Wisata', 'city' => 'Pandeglang', 'slug' => 'lembah-salak', 'favorite' => false, 'trips' => [
            ['title' => 'Open Trip Ujung Kulon & Baduy', 'slug' => 'ujung-kulon', 'type' => 'open-trip', 'destination' => 'Ujung Kulon, Banten', 'meeting_point' => 'Stasiun Rangkasbitung', 'group' => 'nusantara', 'price' => 750000, 'days' => 3, 'capacity' => 16, 'coordinates' => '-6.7567,105.3351', 'overview' => 'Perjalanan Ujung Kulon dan kawasan budaya Banten dalam kelompok kecil bersama pengelola lokal.', 'stops' => ['Stasiun Rangkasbitung', 'Desa Baduy Luar', 'Taman Nasional Ujung Kulon']],
            ['title' => 'Private Trip Tana Toraja', 'slug' => 'tana-toraja-private', 'type' => 'private-trip', 'destination' => 'Tana Toraja, Sulawesi Selatan', 'meeting_point' => 'Bandara Toraja', 'group' => 'nusantara', 'price' => 2000000, 'days' => 4, 'capacity' => 8, 'coordinates' => '-3.0753,119.7426', 'overview' => 'Paket privat Tana Toraja dengan rencana perjalanan yang dapat dikonsultasikan untuk rombongan.', 'stops' => ['Bandara Toraja', 'Kete Kesu', 'Lemo']],
        ]],
        ['name' => 'Pangrango Lokal Trip', 'city' => 'Mataram', 'slug' => 'pangrango-lokal', 'favorite' => false, 'trips' => [
            ['title' => 'Open Trip Rinjani Senaru', 'slug' => 'rinjani', 'type' => 'open-trip', 'destination' => 'Gunung Rinjani, Lombok', 'meeting_point' => 'Basecamp Senaru', 'group' => 'volcano', 'price' => 1750000, 'days' => 4, 'capacity' => 14, 'coordinates' => '-8.4117,116.4570', 'overview' => 'Pendakian Rinjani dari Senaru untuk kelompok kecil dengan briefing dan pendampingan lokal.', 'stops' => ['Basecamp Senaru', 'Pos Pelawangan', 'Danau Segara Anak']],
            ['title' => 'Private Trip Danau Toba', 'slug' => 'danau-toba-private', 'type' => 'private-trip', 'destination' => 'Danau Toba, Sumatera Utara', 'meeting_point' => 'Bandara Silangit', 'group' => 'nusantara', 'price' => 1600000, 'days' => 3, 'capacity' => 10, 'coordinates' => '2.6845,98.8756', 'overview' => 'Perjalanan privat Danau Toba dengan penyesuaian jadwal bersama tim perjalanan lokal.', 'stops' => ['Bandara Silangit', 'Balige', 'Pulau Samosir']],
        ]],
    ];

    public function run(AccessService $access): void
    {
        foreach ($this->vendors as $vendorIndex => $vendorData) {
            $password = Str::password(32);
            $user = User::firstOrCreate(
                ['email' => 'vendor.'.$vendorData['slug'].'@seed.tapaklokal.test'],
                ['name' => $vendorData['name'], 'password' => Hash::make($password), 'phone' => '0812'.str_pad((string) ($vendorIndex + 1), 8, '0', STR_PAD_LEFT), 'city' => $vendorData['city'], 'status' => 'active', 'email_verified_at' => now()],
            );
            $access->grant($user, 'vendor_admin');
            $vendor = Vendor::firstOrCreate(
                ['user_id' => $user->id],
                ['name' => $vendorData['name'], 'city' => $vendorData['city'], 'email' => $user->email, 'phone' => $user->phone, 'description' => 'Mitra perjalanan lokal terverifikasi yang mengelola paket melalui Portal Mitra TapakLokal.', 'status' => 'verified', 'verification_note' => 'Akun katalog vendor aktif untuk pengelolaan trip dan moderasi admin.'],
            );
            $this->seedTrips($vendor, $vendorData, $vendorIndex);
        }

        Cache::forget(PublicContentService::VERSION_KEY);
        $this->command?->info('10 vendor aktif dan 20 paket perjalanan lengkap siap dikelola melalui Portal Mitra.');
    }

    /** @param array{name: string, city: string, slug: string, favorite: bool, trips: array<int, array<string, mixed>>} $vendorData */
    private function seedTrips(Vendor $vendor, array $vendorData, int $vendorIndex): void
    {
        foreach ($vendorData['trips'] as $tripIndex => $tripData) {
            /** @var array{title: string, slug: string, type: string, destination: string, meeting_point: string, group: string, price: int, days: int, capacity: int, coordinates: string, overview: string, stops: array<int, string>} $tripData */
            $photos = $this->photosFor($vendor, $vendorData['slug'], $tripData);
            $departure = today()->addDays(10 + ($vendorIndex * 3) + $tripIndex);
            $trip = Trip::firstOrCreate(['slug' => $tripData['slug']], [
                'vendor_id' => $vendor->id, 'title' => $tripData['title'], 'type' => $tripData['type'], 'destination' => $tripData['destination'],
                'description' => $this->description($tripData), 'itinerary' => $this->plainItinerary($tripData), 'meeting_point' => $tripData['meeting_point'], 'image_url' => $photos[0],
                'departure_date' => $departure, 'end_date' => $departure->copy()->addDays($tripData['days'] - 1), 'capacity' => $tripData['capacity'], 'reserved_seats' => 0,
                'price' => $tripData['price'], 'status' => 'published', 'experience' => $this->experience($tripData, $photos),
            ]);

            if ($trip->departure_date < today() || $trip->status !== 'published') {
                $trip->update([
                    'departure_date' => $departure,
                    'end_date' => $departure->copy()->addDays($tripData['days'] - 1),
                    'status' => 'published',
                ]);
            }

            $this->syncCatalogExperience($trip, $vendor, $tripData, $photos);
            $this->normalizeSeededPhotoUrls($trip);

            if ($vendorData['favorite']) {
                $this->seedReviews($trip, $vendorIndex, $tripData['type'] === 'open-trip');
            }
        }
    }

    private function normalizeSeededPhotoUrls(Trip $trip): void
    {
        $imageUrl = $this->absoluteSeededPhotoUrl((string) $trip->image_url);
        $experience = $this->normalizeSeededPhotoValue($trip->experience);
        if ($imageUrl !== $trip->image_url || $experience !== $trip->experience) {
            $trip->forceFill(['image_url' => $imageUrl, 'experience' => $experience])->save();
        }
    }

    /**
     * Seeded catalog accounts can be upgraded once when the public schema grows.
     * A vendor-created account is never overwritten by this catalog seeder.
     *
     * @param  array{title: string, type: string, destination: string, meeting_point: string, group: string, days: int, coordinates: string, overview: string, stops: array<int, string>}  $tripData
     * @param  array<int, string>  $photos
     */
    private function syncCatalogExperience(Trip $trip, Vendor $vendor, array $tripData, array $photos): void
    {
        if (! Str::endsWith((string) $vendor->email, '@seed.tapaklokal.test') || ($trip->experience['catalog_schema_version'] ?? 0) >= 3) {
            return;
        }

        $trip->forceFill(['experience' => $this->experience($tripData, $photos)])->save();
    }

    /** @param array<string, mixed>|null $value
     * @return array<string, mixed>|null
     */
    private function normalizeSeededPhotoValue(?array $value): ?array
    {
        if ($value === null) {
            return null;
        }
        foreach ($value as $key => $item) {
            $value[$key] = is_array($item) ? $this->normalizeSeededPhotoValue($item) : (is_string($item) ? $this->absoluteSeededPhotoUrl($item) : $item);
        }

        return $value;
    }

    private function absoluteSeededPhotoUrl(string $value): string
    {
        $path = parse_url($value, PHP_URL_PATH);

        return is_string($path) && Str::startsWith($path, ['/storage/trips/jawa-barat/', '/storage/trips/catalog/']) ? url($path) : $value;
    }

    /** @param array{group: string, slug: string, destination: string} $tripData
     * @return array<int, string>
     */
    private function photosFor(Vendor $vendor, string $vendorSlug, array $tripData): array
    {
        $files = $this->photoGroups[$tripData['group']]['files'];

        return array_map(
            fn (string $file, int $index): string => $this->storePhoto($vendor, $vendorSlug, $tripData['slug'], $tripData['destination'], $tripData['group'], $index + 1, $file),
            $files,
            array_keys($files),
        );
    }

    private function storePhoto(Vendor $vendor, string $vendorSlug, string $tripSlug, string $destination, string $group, int $position, string $file): string
    {
        $disk = Storage::disk('public');
        $path = 'trips/catalog/'.$vendorSlug.'-'.$tripSlug.'-'.$position.'.webp';
        if (! $disk->exists($path)) {
            $source = public_path($this->photoGroups[$group]['directory'].'/'.$file);
            $binary = is_file($source) ? file_get_contents($source) : false;
            $input = $binary === false ? false : imagecreatefromstring($binary);
            if (! $input) {
                throw new \RuntimeException('Aset trip tidak dapat dibaca: '.$file);
            }
            $output = imagesx($input) > 1600 ? imagescale($input, 1600, (int) round(imagesy($input) * (1600 / imagesx($input))), IMG_BICUBIC) : $input;
            if (! $output) {
                imagedestroy($input);
                throw new \RuntimeException('Aset trip gagal dioptimalkan: '.$file);
            }
            $stream = fopen('php://temp', 'w+b');
            try {
                if (! imagewebp($output, $stream, 82)) {
                    throw new \RuntimeException('Aset trip gagal dikonversi: '.$file);
                }
                rewind($stream);
                if (! $disk->put($path, $stream)) {
                    throw new \RuntimeException('Aset trip gagal disimpan.');
                }
            } finally {
                fclose($stream);
                if ($output !== $input) {
                    imagedestroy($output);
                }
                imagedestroy($input);
            }
        }
        MediaAsset::firstOrCreate(['disk' => 'public', 'path' => $path], ['user_id' => $vendor->user_id, 'name' => basename($path), 'mime_type' => 'image/webp', 'size' => $disk->size($path), 'alt_text' => 'Foto perjalanan '.$destination, 'visibility' => 'public']);

        return url($disk->url($path));
    }

    /** @param array{title: string, type: string, destination: string, meeting_point: string, group: string, days: int, coordinates: string, overview: string, stops: array<int, string>} $tripData
     * @param  array<int, string>  $photos
     * @return array<string, mixed>
     */
    private function experience(array $tripData, array $photos): array
    {
        $stops = $tripData['stops'];
        $mountain = in_array($tripData['group'], ['salak', 'gede', 'volcano'], true);
        $overnight = $tripData['days'] > 1;

        return [
            'catalog_schema_version' => 3,
            'detail' => ['subtitle' => $tripData['type'] === 'open-trip' ? 'Berangkat bersama kelompok kecil dengan pendampingan mitra lokal.' : 'Perjalanan privat yang dapat dikoordinasikan untuk kebutuhan rombongan.', 'images' => $photos, 'coordinates' => $tripData['coordinates'], 'meetingTime' => '06.30 WIB', 'arrivalNote' => 'Hadir 30 menit sebelum briefing keselamatan.', 'meetingNote' => 'Temui pemandu di '.$tripData['meeting_point'].' dan tunjukkan bukti pemesanan.'],
            'description_html' => '<h3>'.e($tripData['title']).'</h3><p>'.e($tripData['overview']).'</p><p>Galeri, fasilitas, itinerary, dan jadwal dikelola langsung oleh vendor melalui Portal Mitra TapakLokal.</p>',
            'itinerary_html' => '<h3>Rencana perjalanan</h3><p>Susunan kegiatan mengikuti kondisi lapangan, keselamatan peserta, serta arahan pemandu lokal.</p>',
            'highlights' => [
                ['title' => 'Briefing & persiapan', 'time' => 'Hari 1 · 06.30', 'location' => $tripData['meeting_point'], 'description' => 'Registrasi peserta, pengecekan perlengkapan, dan penjelasan alur perjalanan.', 'icon' => 'ShieldCheck', 'images' => [$photos[0], $photos[1]]],
                ['title' => $stops[1], 'time' => 'Hari 1 · 10.00', 'location' => $tripData['destination'], 'description' => 'Jelajahi titik utama bersama pemandu dengan tempo yang disesuaikan kondisi rombongan.', 'icon' => $mountain ? 'Compass' : 'MapPin', 'images' => [$photos[2], $photos[3]]],
                ['title' => 'Dokumentasi perjalanan', 'time' => 'Hari '.$tripData['days'].' · 15.00', 'location' => $stops[2], 'description' => 'Waktu untuk menikmati lokasi, beristirahat, dan dokumentasi kelompok.', 'icon' => 'Camera', 'images' => [$photos[4], $photos[0]]],
            ],
            'destinations' => [
                ['name' => $stops[0], 'subtitle' => 'Titik awal perjalanan', 'time' => '06.30 WIB', 'activity' => 'Registrasi dan briefing', 'note' => 'Siapkan identitas, perlengkapan pribadi, dan bukti pemesanan.', 'coordinates' => $tripData['coordinates'], 'image_url' => $photos[0]],
                ['name' => $stops[1], 'subtitle' => 'Titik utama', 'time' => '10.00 WIB', 'activity' => 'Aktivitas utama bersama pemandu', 'note' => 'Tetap bersama rombongan dan ikuti arahan pemandu.', 'image_url' => $photos[2]],
                ['name' => $stops[2], 'subtitle' => 'Penutup perjalanan', 'time' => '15.00 WIB', 'activity' => 'Istirahat dan dokumentasi', 'note' => 'Waktu dapat berubah mengikuti kondisi lapangan.', 'image_url' => $photos[4]],
            ],
            'itineraryDays' => $this->itineraryDays($tripData),
            'facilityDetails' => [['title' => 'Pemandu lokal', 'category' => 'Layanan', 'note' => 'Mendampingi peserta sepanjang rangkaian aktivitas.'], ['title' => $overnight ? 'Akomodasi sesuai paket' : 'Konsumsi sesuai paket', 'category' => 'Kenyamanan', 'note' => 'Rincian akhir dikonfirmasi pada informasi keberangkatan.'], ['title' => 'P3K kelompok', 'category' => 'Keamanan', 'note' => 'Disediakan untuk kebutuhan pertolongan pertama selama kegiatan.']],
            'facilities' => [['label' => 'Pemandu lokal', 'icon' => 'Users'], ['label' => $overnight ? 'Akomodasi' : 'Konsumsi', 'icon' => $overnight ? 'BedDouble' : 'Utensils'], ['label' => 'Dokumentasi', 'icon' => 'Camera'], ['label' => 'P3K', 'icon' => 'ShieldCheck']],
            'included' => ['Pemandu lokal', 'Dokumentasi kelompok', $overnight ? 'Akomodasi sesuai paket' : 'Konsumsi sesuai paket', 'P3K kelompok'],
            'excluded' => ['Transportasi menuju titik kumpul', 'Pengeluaran pribadi', 'Asuransi perjalanan pribadi'],
            'packingItems' => $mountain ? ['Sepatu trekking', 'Jas hujan', 'Air minum minimal 1,5 liter', 'Obat pribadi', 'Baju ganti'] : ['Pakaian nyaman', 'Sandal atau sepatu antiselip', 'Tabir surya', 'Obat pribadi', 'Baju ganti'],
            'faqs' => [['question' => 'Apakah paket ini cocok untuk pemula?', 'answer' => 'Cocok selama peserta dalam kondisi sehat dan mengikuti arahan pemandu sepanjang perjalanan.'], ['question' => 'Bagaimana jika kondisi cuaca berubah?', 'answer' => 'Vendor dapat menyesuaikan urutan kegiatan demi keselamatan sesuai kondisi di lapangan.'], ['question' => 'Kapan detail keberangkatan dikonfirmasi?', 'answer' => 'Rincian titik kumpul dan persiapan akhir dikirimkan vendor setelah pesanan terkonfirmasi.']],
            'panoramas' => in_array($tripData['group'], ['salak', 'gede'], true)
                ? [['title' => $stops[0], 'label' => 'Kenali area titik kumpul', 'image_url' => $photos[0]], ['title' => $stops[1], 'label' => 'Pratinjau suasana perjalanan', 'image_url' => $photos[2]]]
                : [],
        ];
    }

    /** @param array{days: int, stops: array<int, string>} $tripData
     * @return array<int, array{day: string, meals: string, activities: array<int, string>}>
     */
    private function itineraryDays(array $tripData): array
    {
        $days = [];
        for ($day = 1; $day <= $tripData['days']; $day++) {
            $days[] = ['day' => 'Hari '.$day, 'meals' => $day === 1 ? 'Makan sesuai paket' : 'Sarapan dan makan sesuai paket', 'activities' => match (true) {
                $day === 1 => ['06.30 Registrasi di '.$tripData['stops'][0], '08.00 Briefing dan mulai perjalanan bersama pemandu', '10.00 Aktivitas utama di '.$tripData['stops'][1]],
                $day === $tripData['days'] => ['08.00 Persiapan perjalanan pulang', '10.00 Aktivitas penutup di '.$tripData['stops'][2], '15.00 Perjalanan selesai dan kembali ke titik akhir'],
                default => ['08.00 Aktivitas lanjutan bersama pemandu', '12.00 Istirahat dan makan sesuai paket', '15.00 Dokumentasi serta evaluasi rute'],
            }];
        }

        return $days;
    }

    /** @param array{type: string, destination: string, overview: string} $tripData */
    private function description(array $tripData): string
    {
        return ($tripData['type'] === 'open-trip' ? 'Open trip ' : 'Private trip ').'ke '.$tripData['destination'].'. '.$tripData['overview'].' Galeri, fasilitas, itinerary, dan jadwal dapat diperbarui vendor melalui Portal Mitra.';
    }

    /** @param array{stops: array<int, string>} $tripData */
    private function plainItinerary(array $tripData): string
    {
        return implode("\n", ['06.30 Registrasi di '.$tripData['stops'][0], '08.00 Briefing dan mulai perjalanan', '10.00 Aktivitas utama di '.$tripData['stops'][1], '15.00 Penutup perjalanan di '.$tripData['stops'][2]]);
    }

    private function seedReviews(Trip $trip, int $vendorIndex, bool $isOpenTrip): void
    {
        foreach ([5, 5, 5, 4] as $reviewIndex => $rating) {
            $user = User::firstOrCreate(['email' => 'reviewer.jabar.'.($vendorIndex + 1).'.'.($reviewIndex + 1).'@seed.tapaklokal.test'], ['name' => ['Rani', 'Dimas', 'Nadia', 'Fajar'][$reviewIndex].' Wisatawan', 'password' => Hash::make(Str::random(32)), 'status' => 'active', 'email_verified_at' => now()]);
            $booking = Booking::firstOrCreate(['reference' => 'SEED-JB-'.$trip->id.'-'.($reviewIndex + 1)], ['idempotency_key' => (string) Str::uuid(), 'user_id' => $user->id, 'trip_id' => $trip->id, 'vendor_id' => $trip->vendor_id, 'participants' => 2, 'contact_name' => $user->name, 'contact_phone' => '08130000'.str_pad((string) ($reviewIndex + 1), 4, '0', STR_PAD_LEFT), 'subtotal' => $trip->price * 2, 'total' => $trip->price * 2, 'platform_fee' => 0, 'vendor_amount' => $trip->price * 2, 'status' => 'completed', 'expires_at' => now()->subDays(30)]);
            Review::firstOrCreate(['booking_id' => $booking->id], ['user_id' => $user->id, 'trip_id' => $trip->id, 'rating' => $rating, 'body' => $isOpenTrip ? 'Pemandu komunikatif, jadwal rapi, dan perjalanan terasa aman dari awal sampai selesai.' : 'Rombongan kami nyaman karena jadwal dapat disesuaikan dan pemandu sangat membantu.', 'vendor_response' => 'Terima kasih sudah mempercayakan perjalanan kepada mitra lokal kami.', 'status' => 'published']);
        }
    }
}
