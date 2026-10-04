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
    /** @var array<int, string> */
    private array $salakPhotos = ['mt-salak-parkiran.jpg', 'mt-salak-regist-toilet.jpg', 'mt-salak-pintu-rimba-ceksampah.jpg', 'parkiran-curug.jpg', 'curug.jpg'];

    /** @var array<int, string> */
    private array $gedePhotos = ['mt-gede-basecamp.jpg', 'mt-gede-parkiran-atas.jpg', 'mt-gede-parkiran-basecamp.jpg', 'mt-gede-parkiran-bawah.jpg', 'mt-gede-pintu rimba&regis.jpg'];

    /** @var array<int, array{name: string, city: string, slug: string, favorite: bool}> */
    private array $vendors = [
        ['name' => 'Salak Rimba Adventure', 'city' => 'Bogor', 'slug' => 'salak-rimba', 'favorite' => true],
        ['name' => 'Gede Pangrango Explorer', 'city' => 'Cianjur', 'slug' => 'gede-explorer', 'favorite' => true],
        ['name' => 'Puncak Hijau Trip', 'city' => 'Sukabumi', 'slug' => 'puncak-hijau', 'favorite' => false],
        ['name' => 'Sunda Trekking Club', 'city' => 'Bandung', 'slug' => 'sunda-trekking', 'favorite' => false],
        ['name' => 'Bogor Nature Walk', 'city' => 'Bogor', 'slug' => 'bogor-nature', 'favorite' => false],
        ['name' => 'Cianjur Mountain Guide', 'city' => 'Cianjur', 'slug' => 'cianjur-guide', 'favorite' => false],
        ['name' => 'Jalur Hutan Jawa Barat', 'city' => 'Depok', 'slug' => 'jalur-hutan-jabar', 'favorite' => false],
        ['name' => 'Rimba Nusantara Tour', 'city' => 'Bekasi', 'slug' => 'rimba-nusantara', 'favorite' => false],
        ['name' => 'Lembah Salak Wisata', 'city' => 'Sukabumi', 'slug' => 'lembah-salak', 'favorite' => false],
        ['name' => 'Pangrango Lokal Trip', 'city' => 'Cianjur', 'slug' => 'pangrango-lokal', 'favorite' => false],
    ];

    public function run(AccessService $access): void
    {
        $password = (string) env('SEED_VENDOR_PASSWORD');
        if ($password === '' && ! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Set SEED_VENDOR_PASSWORD before running this production seeder.');
        }
        $password = $password ?: 'VendorTrip2026!';

        foreach ($this->vendors as $index => $vendorData) {
            $user = User::firstOrCreate(
                ['email' => 'vendor.'.$vendorData['slug'].'@seed.tapaklokal.test'],
                ['name' => $vendorData['name'], 'password' => Hash::make($password), 'phone' => '0812'.str_pad((string) ($index + 1), 8, '0', STR_PAD_LEFT), 'city' => $vendorData['city'], 'status' => 'active', 'email_verified_at' => now()],
            );
            $access->grant($user, 'vendor_admin');
            $vendor = Vendor::firstOrCreate(
                ['user_id' => $user->id],
                ['name' => $vendorData['name'], 'city' => $vendorData['city'], 'email' => $user->email, 'phone' => $user->phone, 'description' => 'Mitra perjalanan lokal Jawa Barat dengan pemandu terverifikasi, jadwal jelas, dan informasi perjalanan yang dapat dikelola melalui Portal Mitra TapakLokal.', 'status' => 'verified', 'verification_note' => 'Akun katalog Jawa Barat aktif untuk pengelolaan trip dan moderasi admin.'],
            );
            $this->seedTrips($vendor, $vendorData, $index);
        }

        Cache::forget(PublicContentService::VERSION_KEY);
        $this->command?->info('10 vendor aktif dan 20 trip Jawa Barat siap dikelola melalui Portal Mitra.');
    }

    /** @param array{name: string, city: string, slug: string, favorite: bool} $vendorData */
    private function seedTrips(Vendor $vendor, array $vendorData, int $vendorIndex): void
    {
        foreach (['open-trip', 'private-trip'] as $typeIndex => $type) {
            $mountain = ($vendorIndex + $typeIndex) % 2 === 0 ? 'salak' : 'gede';
            $photos = $this->photosFor($vendor, $vendorData['slug'], $mountain);
            $slug = $vendorData['slug'].'-'.$type;
            $isOpenTrip = $type === 'open-trip';
            $mountainName = $mountain === 'salak' ? 'Gunung Salak' : 'Gunung Gede Pangrango';
            $destination = $mountain === 'salak' ? 'Gunung Salak, Bogor' : 'Gunung Gede Pangrango, Cianjur';
            $departure = today()->addDays(10 + ($vendorIndex * 3) + $typeIndex);
            $trip = Trip::firstOrCreate(
                ['slug' => $slug],
                ['vendor_id' => $vendor->id, 'title' => ($isOpenTrip ? 'Open Trip ' : 'Private Trip ').$mountainName.' · '.$vendorData['name'], 'type' => $type, 'destination' => $destination, 'description' => $this->description($mountainName, $isOpenTrip), 'itinerary' => "07.00 Registrasi dan briefing\n08.00 Mulai trekking bersama pemandu\n12.00 Istirahat dan makan siang\n15.00 Kembali ke titik kumpul", 'meeting_point' => $mountain === 'salak' ? 'Pos Registrasi Gunung Salak' : 'Basecamp Gunung Gede', 'image_url' => $photos[0], 'departure_date' => $departure, 'end_date' => $departure->copy()->addDays($isOpenTrip ? 1 : 2), 'capacity' => $isOpenTrip ? 18 : 10, 'reserved_seats' => 0, 'price' => $isOpenTrip ? 325000 + ($vendorIndex * 10000) : 2900000 + ($vendorIndex * 50000), 'status' => 'published', 'experience' => $this->experience($mountainName, $destination, $photos, $isOpenTrip)],
            );
            $this->normalizeSeededPhotoUrls($trip);
            if ($vendorData['favorite']) {
                $this->seedReviews($trip, $vendorIndex, $isOpenTrip);
            }
        }
    }

    private function normalizeSeededPhotoUrls(Trip $trip): void
    {
        $imageUrl = $this->relativeSeededPhotoUrl((string) $trip->image_url);
        $experience = $this->normalizeSeededPhotoValue($trip->experience);

        if ($imageUrl !== $trip->image_url || $experience !== $trip->experience) {
            $trip->forceFill([
                'image_url' => $imageUrl,
                'experience' => $experience,
            ])->save();
        }
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
            if (is_array($item)) {
                $value[$key] = $this->normalizeSeededPhotoValue($item);
            } elseif (is_string($item)) {
                $value[$key] = $this->relativeSeededPhotoUrl($item);
            }
        }

        return $value;
    }

    private function relativeSeededPhotoUrl(string $value): string
    {
        $path = parse_url($value, PHP_URL_PATH);

        return is_string($path) && Str::startsWith($path, '/storage/trips/jawa-barat/') ? $path : $value;
    }

    /** @return array<int, string> */
    private function photosFor(Vendor $vendor, string $vendorSlug, string $mountain): array
    {
        return array_map(fn (string $filename, int $index): string => $this->storePhoto($vendor, $vendorSlug, $mountain, $index + 1, $filename), $mountain === 'salak' ? $this->salakPhotos : $this->gedePhotos, array_keys($mountain === 'salak' ? $this->salakPhotos : $this->gedePhotos));
    }

    private function storePhoto(Vendor $vendor, string $vendorSlug, string $mountain, int $position, string $filename): string
    {
        $disk = Storage::disk('public');
        $path = 'trips/jawa-barat/'.$vendorSlug.'-'.$mountain.'-'.$position.'.webp';
        if (! $disk->exists($path)) {
            $source = public_path('Assets/Images/360/'.$filename);
            if (! is_file($source)) {
                throw new \RuntimeException('Aset 360 tidak ditemukan: '.$filename);
            }
            $input = imagecreatefromstring(file_get_contents($source));
            if (! $input) {
                throw new \RuntimeException('Aset 360 tidak dapat dibaca: '.$filename);
            }
            $width = min(1600, imagesx($input));
            $output = imagescale($input, $width, (int) round(imagesy($input) * ($width / imagesx($input))), IMG_BICUBIC);
            $stream = fopen('php://temp', 'w+b');
            try {
                if (! $output || ! imagewebp($output, $stream, 82)) {
                    throw new \RuntimeException('Aset trip gagal dioptimalkan: '.$filename);
                }
                rewind($stream);
                if (! $disk->put($path, $stream)) {
                    throw new \RuntimeException('Aset trip gagal disimpan.');
                }
            } finally {
                fclose($stream);
                imagedestroy($input);
                if ($output) {
                    imagedestroy($output);
                }
            }
        }
        MediaAsset::firstOrCreate(['disk' => 'public', 'path' => $path], ['user_id' => $vendor->user_id, 'name' => basename($path), 'mime_type' => 'image/webp', 'size' => $disk->size($path), 'alt_text' => 'Foto perjalanan '.$mountain.' Jawa Barat', 'visibility' => 'public']);

        return $disk->url($path);
    }

    /** @param array<int, string> $photos
     * @return array<string, mixed>
     */
    private function experience(string $mountain, string $destination, array $photos, bool $isOpenTrip): array
    {
        return [
            'detail' => ['subtitle' => ($isOpenTrip ? 'Berangkat bersama kelompok kecil.' : 'Jadwal privat yang fleksibel untuk rombonganmu.').' Rute dipandu tim lokal berpengalaman.', 'images' => $photos, 'coordinates' => $mountain === 'Gunung Salak' ? '-6.6620,106.7000' : '-6.7307,106.9846', 'meetingTime' => '07.00 WIB', 'arrivalNote' => 'Hadir 30 menit sebelum briefing keselamatan.', 'meetingNote' => 'Tunjukkan bukti pemesanan kepada pemandu di titik kumpul.'],
            'description_html' => '<h3>Jelajah alam Jawa Barat bersama mitra lokal</h3><p>Perjalanan ini memadukan trekking, edukasi lingkungan, dan waktu beristirahat di jalur yang telah direncanakan.</p>',
            'itinerary_html' => '<h3>Rencana perjalanan</h3><p>Rute dapat disesuaikan dengan kondisi cuaca dan arahan petugas kawasan.</p>',
            'highlights' => [['title' => 'Briefing & persiapan', 'time' => 'Hari 1 · 07.00', 'location' => 'Titik kumpul', 'description' => 'Pengecekan perlengkapan dan pengenalan rute.', 'icon' => 'ShieldCheck', 'images' => [$photos[0], $photos[1]]], ['title' => 'Trekking bersama pemandu', 'time' => 'Hari 1 · 08.00', 'location' => $destination, 'description' => 'Jelajahi jalur dengan tempo aman dan jeda teratur.', 'icon' => 'Compass', 'images' => [$photos[2], $photos[3]]], ['title' => 'Istirahat & dokumentasi', 'time' => 'Hari 1 · 12.00', 'location' => 'Area aman', 'description' => 'Nikmati makan siang dan dokumentasi kelompok.', 'icon' => 'Camera', 'images' => [$photos[4], $photos[0]]]],
            'destinations' => [['name' => 'Titik registrasi', 'subtitle' => 'Awal perjalanan', 'time' => '07.00 WIB', 'activity' => 'Registrasi dan briefing', 'note' => 'Siapkan identitas dan air minum.', 'image_url' => $photos[0]], ['name' => 'Jalur hutan', 'subtitle' => 'Trekking', 'time' => '08.00 WIB', 'activity' => 'Trekking bersama pemandu', 'note' => 'Tetap bersama rombongan.', 'image_url' => $photos[2]], ['name' => 'Area istirahat', 'subtitle' => 'Makan siang', 'time' => '12.00 WIB', 'activity' => 'Istirahat dan dokumentasi', 'note' => 'Ikuti arahan pemandu.', 'image_url' => $photos[4]]],
            'itineraryDays' => [['day' => 'Hari 1', 'meals' => 'Makan siang', 'activities' => ['07.00 Registrasi dan briefing', '08.00 Mulai trekking', '12.00 Istirahat dan makan siang', '15.00 Kembali ke titik kumpul']]],
            'facilityDetails' => [['title' => 'Pemandu lokal', 'category' => 'Layanan', 'note' => 'Mendampingi peserta selama kegiatan.'], ['title' => 'Makan siang', 'category' => 'Konsumsi', 'note' => 'Satu porsi per peserta.'], ['title' => 'P3K kelompok', 'category' => 'Keamanan', 'note' => 'Dibawa oleh pemandu.']],
            'facilities' => [['label' => 'Pemandu', 'icon' => 'Users'], ['label' => 'Makan siang', 'icon' => 'Utensils'], ['label' => 'Dokumentasi', 'icon' => 'Camera'], ['label' => 'P3K', 'icon' => 'ShieldCheck']],
            'included' => ['Pemandu lokal', 'Makan siang', 'Dokumentasi kelompok', 'P3K kelompok'], 'excluded' => ['Transportasi ke titik kumpul', 'Pengeluaran pribadi', 'Asuransi perjalanan pribadi'], 'packingItems' => ['Sepatu trekking', 'Jas hujan', 'Air minum minimal 1,5 liter', 'Obat pribadi', 'Baju ganti'],
            'faqs' => [['question' => 'Apakah cocok untuk pemula?', 'answer' => 'Rute dapat disesuaikan, tetapi peserta perlu dalam kondisi sehat dan mengikuti arahan pemandu.'], ['question' => 'Bagaimana jika hujan?', 'answer' => 'Keputusan rute mengikuti kondisi lapangan dan arahan pemandu.']],
            'panoramas' => [['title' => 'Titik awal perjalanan', 'label' => 'Kenali area registrasi', 'image_url' => $photos[0]], ['title' => 'Jalur perjalanan', 'label' => 'Lihat suasana rute', 'image_url' => $photos[2]]],
        ];
    }

    private function description(string $mountain, bool $isOpenTrip): string
    {
        return ($isOpenTrip ? 'Open trip kelompok kecil ' : 'Private trip untuk rombongan ').'ke '.$mountain.' bersama pemandu lokal Jawa Barat. Jadwal, galeri, fasilitas, dan detail perjalanan dapat diperbarui langsung oleh vendor melalui Portal Mitra.';
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
