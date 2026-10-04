<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Models\Trip;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompleteTripDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Trip demo hanya boleh dibuat di local/testing.');
        }
        $vendor = Vendor::where('status', 'verified')->whereHas('user', fn ($query) => $query->where('name', 'ahmadun'))->sole();
        $files = ['mt-salak-parkiran.jpg', 'mt-salak-regist-toilet.jpg', 'mt-salak-pintu-rimba-ceksampah.jpg', 'parkiran-curug.jpg', 'curug.jpg'];
        $photos = [];
        foreach ($files as $index => $file) {
            $source = public_path('Assets/Images/360/'.$file);
            $path = $this->storeOptimizedPhoto($source, $index + 1, $file);
            $photos[] = url(Storage::disk('public')->url($path));
        }
        DB::transaction(function () use ($vendor, $files, $photos): void {
            foreach ($files as $index => $file) {
                $path = 'trips/demo-salak-'.($index + 1).'.webp';
                MediaAsset::updateOrCreate(['disk' => 'public', 'path' => $path], ['user_id' => $vendor->user_id, 'name' => pathinfo($file, PATHINFO_FILENAME).'.webp', 'mime_type' => 'image/webp', 'size' => Storage::disk('public')->size($path), 'alt_text' => 'Foto referensi jalur Salak dan curug: '.$file, 'visibility' => 'public']);
            }
            $description = '<h3>Sehari menjelajahi alam kaki Gunung Salak</h3><p>Contoh paket perjalanan untuk mencoba seluruh fitur detail trip TapakLokal. Jelajahi jalur hutan, kenali area registrasi, dan beristirahat di kawasan curug bersama pemandu lokal.</p><ul><li>Kelompok kecil maksimal 12 peserta.</li><li>Briefing dan pemeriksaan perlengkapan sebelum berjalan.</li><li>Waktu untuk beristirahat dan dokumentasi.</li></ul><p><strong>Data demo:</strong> jadwal, harga, rute, dan fasilitas merupakan contoh pengisian, bukan penawaran operasional. Foto curug adalah referensi kawasan; lokasi akhir harus dikonfirmasi penyelenggara.</p>';
            $experience = [
                'detail' => ['subtitle' => 'Trekking hutan dan jelajah curug dalam satu hari — contoh trip lengkap.', 'images' => $photos, 'coordinates' => '-6.6620,106.7000', 'meetingTime' => '07.00 WIB', 'arrivalNote' => 'Hadir 30 menit sebelum briefing.', 'meetingNote' => 'Temui pemandu di area parkir registrasi. Koordinat contoh; pastikan titik aktual kepada penyelenggara.'],
                'description_html' => $description,
                'itinerary_html' => '<h3>Alur kegiatan</h3><p>Perjalanan mengikuti kondisi cuaca dan arahan petugas setempat. Peserta wajib mengikuti briefing keselamatan.</p>',
                'highlights' => [
                    ['title' => 'Persiapan dan briefing', 'time' => 'Hari 1 · 07.00', 'location' => 'Area registrasi Salak', 'description' => 'Kenali rute, periksa perlengkapan, dan lakukan pemanasan bersama pemandu.', 'icon' => 'ShieldCheck', 'images' => [$photos[0], $photos[1]]],
                    ['title' => 'Trekking pintu rimba', 'time' => 'Hari 1 · 08.00', 'location' => 'Jalur hutan Gunung Salak', 'description' => 'Nikmati suasana hutan dengan tempo santai dan jeda istirahat teratur.', 'icon' => 'Compass', 'images' => [$photos[2], $photos[1]]],
                    ['title' => 'Jelajah kawasan curug', 'time' => 'Hari 1 · 12.00', 'location' => 'Kawasan curug', 'description' => 'Beristirahat dan mengambil foto dari area aman yang ditentukan pemandu.', 'icon' => 'Camera', 'images' => [$photos[4], $photos[3]]],
                ],
                'destinations' => [
                    ['name' => 'Registrasi Gunung Salak', 'subtitle' => 'Titik awal', 'time' => '07.00 WIB', 'activity' => 'Registrasi dan briefing', 'note' => 'Siapkan identitas dan air minum.', 'image_url' => $photos[1]],
                    ['name' => 'Pintu rimba Salak', 'subtitle' => 'Jalur trekking', 'time' => '08.00 WIB', 'activity' => 'Trekking bersama pemandu', 'note' => 'Jangan meninggalkan rombongan.', 'image_url' => $photos[2]],
                    ['name' => 'Kawasan curug', 'subtitle' => 'Istirahat dan dokumentasi', 'time' => '12.00 WIB', 'activity' => 'Makan siang dan foto', 'note' => 'Foto referensi; lokasi mengikuti konfirmasi vendor.', 'image_url' => $photos[4]],
                ],
                'itineraryDays' => [['day' => 'Hari 1', 'meals' => 'Makan siang', 'activities' => ['07.00 Registrasi dan briefing', '08.00 Mulai trekking', '10.00 Istirahat dan dokumentasi', '12.00 Makan siang di kawasan curug', '14.00 Kembali menuju registrasi', '16.00 Perjalanan selesai']]],
                'facilityDetails' => [['title' => 'Pemandu lokal', 'category' => 'Layanan', 'note' => 'Mendampingi peserta selama kegiatan.'], ['title' => 'Makan siang', 'category' => 'Konsumsi', 'note' => 'Satu paket per peserta.'], ['title' => 'Tiket kawasan', 'category' => 'Tiket', 'note' => 'Sesuai rute contoh.']],
                'facilities' => [['label' => 'Pemandu', 'icon' => 'Users'], ['label' => 'Makan siang', 'icon' => 'Utensils'], ['label' => 'Dokumentasi', 'icon' => 'Camera'], ['label' => 'P3K', 'icon' => 'ShieldCheck']],
                'included' => ['Dokumentasi kelompok', 'Perlengkapan P3K kelompok'], 'excluded' => ['Transportasi menuju titik kumpul', 'Pengeluaran pribadi'],
                'packingItems' => ['Sepatu trekking', 'Jas hujan', 'Air minum minimal 1,5 liter', 'Obat pribadi', 'Baju ganti', 'Kantong sampah'],
                'faqs' => [['question' => 'Apakah trip ini bisa langsung dipesan?', 'answer' => 'Ini data demo untuk pengujian platform, bukan perjalanan operasional.'], ['question' => 'Apakah cocok untuk pemula?', 'answer' => 'Contoh rute disusun untuk peserta yang terbiasa berjalan. Kondisi kesehatan dan kesiapan fisik perlu dikonfirmasi kepada vendor.'], ['question' => 'Bagaimana jika cuaca buruk?', 'answer' => 'Kegiatan mengikuti arahan petugas dan pemandu. Konfirmasi kebijakan perubahan jadwal sebelum pemesanan.']],
                'panoramas' => [['title' => 'Area parkir Salak', 'label' => 'Kenali titik awal', 'image_url' => $photos[0]], ['title' => 'Registrasi', 'label' => 'Fasilitas awal perjalanan', 'image_url' => $photos[1]], ['title' => 'Pintu rimba', 'label' => 'Awal jalur hutan', 'image_url' => $photos[2]]],
            ];
            $trip = Trip::updateOrCreate(['slug' => 'demo-jelajah-gunung-salak-1-hari'], ['vendor_id' => $vendor->id, 'title' => '[DEMO] Jelajah Gunung Salak & Curug', 'type' => 'open-trip', 'destination' => 'Gunung Salak, Bogor', 'description' => strip_tags($description), 'itinerary' => 'Registrasi, trekking hutan, makan siang, jelajah curug, dan kembali ke titik kumpul.', 'meeting_point' => 'Area registrasi Gunung Salak', 'image_url' => $photos[0], 'departure_date' => now()->addMonth()->startOfDay(), 'end_date' => now()->addMonth()->startOfDay(), 'capacity' => 12, 'reserved_seats' => 0, 'price' => 250000, 'status' => 'published', 'experience' => $experience]);
            $this->command?->info('Trip demo siap: '.$trip->slug.' | Vendor: '.$vendor->name);
        });
    }

    private function storeOptimizedPhoto(string $source, int $index, string $name): string
    {
        if (! is_file($source)) {
            throw new \RuntimeException('Gambar demo tidak tersedia: '.$name);
        }

        $dimensions = getimagesize($source);
        if (! $dimensions) {
            throw new \RuntimeException('Gambar demo tidak tersedia: '.$name);
        }

        $input = imagecreatefromstring(file_get_contents($source));
        if (! $input) {
            throw new \RuntimeException('Gambar demo tidak dapat dibaca: '.$name);
        }

        $width = min(1600, $dimensions[0]);
        $height = (int) round($dimensions[1] * ($width / $dimensions[0]));
        $output = imagescale($input, $width, $height, IMG_BICUBIC);
        $stream = fopen('php://temp', 'w+b');
        try {
            if (! $output || ! imagewebp($output, $stream, 82)) {
                throw new \RuntimeException('Gagal mengoptimalkan gambar demo: '.$name);
            }
            rewind($stream);
            $path = 'trips/demo-salak-'.$index.'.webp';
            if (! Storage::disk('public')->put($path, $stream)) {
                throw new \RuntimeException('Gagal menyimpan gambar demo.');
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
