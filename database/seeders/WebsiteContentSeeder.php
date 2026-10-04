<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\ContentPage;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VirtualTour;
use App\Services\AccessService;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteContentSeeder extends Seeder
{
    public function run(AccessService $access, AuditService $audit): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Seeder data contoh hanya untuk local/testing.');
        }
        $source = json_decode(file_get_contents(__DIR__.'/data/website-content.json'), true, 512, JSON_THROW_ON_ERROR);
        $credentials = DB::transaction(function () use ($source, $access, $audit) {
            $email = config('demo.vendor_email');
            $owner = User::where('email', $email)->first();
            if ($owner && ! $owner->roles()->where('name', 'vendor_admin')->exists()) {
                if ($owner->roles()->exists() || $owner->vendor()->exists()) {
                    throw new \RuntimeException('Email vendor contoh sudah dipakai akun lain.');
                }

                $access->grant($owner, 'vendor_admin');
            }
            if (! $owner) {
                $password = config('demo.vendor_password');
                $password ??= Str::password(24);
                $owner = User::create(['name' => 'Vendor Demo Brenggo', 'email' => $email, 'password' => $password]);
                $access->grant($owner, 'vendor_admin');
                $credentials = ['email' => $email, 'password' => $password];
            }
            $vendor = Vendor::firstOrCreate(['user_id' => $owner->id], ['name' => 'BRENGGO.ID', 'city' => 'Malang', 'email' => $email, 'phone' => '080000000000', 'description' => 'Vendor contoh dari desain TapakLokal untuk mencoba pengelolaan trip.', 'status' => 'verified', 'verification_note' => 'Data contoh lokal, bukan hasil verifikasi mitra produksi.']);
            $image = fn (string $id) => 'https://images.unsplash.com/photo-'.$id.'?auto=format&fit=crop&w=1400&q=85';

            // The original mockup used "bali" for both a destination and an article.
            // Give seeded destinations their own public slug before creating CMS articles.
            ContentPage::withTrashed()
                ->where('type', 'destination')
                ->where('slug', 'not like', 'destinasi-%')
                ->whereNotNull('seed_key')
                ->get()
                ->each(function (ContentPage $destination): void {
                    $slug = 'destinasi-'.Str::slug($destination->title);
                    if (! ContentPage::withTrashed()->where('slug', $slug)->exists()) {
                        $destination->forceFill(['slug' => $slug, 'seed_key' => 'page:'.$slug])->save();
                    }
                });

            $pages = [
                ['homepage', 'hero', 'Jelajahi Indonesia Secara Otentik.', 'Temukan destinasi dan pengalaman bersama komunitas lokal.', $image('1537996194471-e657df975ab4')],
                ['homepage', 'promo-merdeka', 'Merdeka Explore', 'Promo spesial kemerdekaan TapakLokal.', '/Assets/Images/benner/Benner-17an.svg'],
            ];
            foreach ([
                ['Labuan Bajo', '1516690561799-46d8f74f9abf', 'Gerbang wisata bahari Taman Nasional Komodo, Pulau Padar, dan pantai pink Flores.'],
                ['Gunung Bromo', '1588668214407-6ea9a6d8c272', 'Lautan pasir vulkanik dan panorama kawah magis Bromo Tengger Semeru.'],
                ['Bali', '1537996194471-e657df975ab4', 'Keindahan pura, pesisir tenang, dan kekayaan budaya adiluhung Pulau Dewata.'],
                ['Yogyakarta', '1584810359583-96fc3448beaa', 'Jantung kebudayaan Jawa dengan keraton, candi megah, dan kehangatan warga lokal.'],
                ['Lombok', '1518548419970-58e3b4079ab2', 'Pesisir berpasir putih, bukit eksotis, dan keanggunan Gunung Rinjani.'],
                ['Raja Ampat', '1516690561799-46d8f74f9abf', 'Gugusan pulau karang karst dan terumbu karang terindah di dunia.'],
                ['Pulau Komodo', '1518548419970-58e3b4079ab2', 'Habitat asli satwa purba komodo dan keajaiban savana tepi laut NTT.'],
                ['Tana Toraja', '1544551763-46a013bb70d5', 'Kekayaan budaya megalitikum, rumah Tongkonan, dan perbukitan Sulawesi Selatan.'],
                ['Kepulauan Derawan', '1507525428034-b723cf961d3e', 'Surga bawah laut penyu hijau dan danau ubur-ubur tanpa sengat Kakaban.'],
                ['Banda Neira', '1518548419970-58e3b4079ab2', 'Sejarah kejayaan rempah pala berpadu keindahan laut biru Maluku.'],
                ['Kepulauan Seribu', '1516690561799-46d8f74f9abf', 'Pelarian pulau tropis dekat ibukota dengan pasir putih dan biota laut.'],
                ['Jawa Barat', '1501179691627-eeaa65ea017c', 'Hamparan kebun teh berkabut, kawah belerang, dan jalur alam pegunungan Sunda.'],
                ['Gunung Salak', '1501179691627-eeaa65ea017c', 'Jalur trekking rimba, pos registrasi, curug eksotis, dan keindahan alam pegunungan Salak Endah Bogor.'],
                ['Gunung Gede', '1588668214407-6ea9a6d8c272', 'Taman Nasional Gunung Gede Pangrango dengan Surya Kencana dan panorama lembah berkabut.'],
            ] as [$name, $photo, $desc]) {
                $pages[] = ['destination', 'destinasi-'.Str::slug($name), $name, $desc, $image($photo)];
            }
            foreach ([['Pulau Pramuka', '1516690561799-46d8f74f9abf'], ['Pulau Pari', '1518548419970-58e3b4079ab2'], ['Pulau Tidung', '1546026423-cc4642628d2b'], ['Pulau Harapan', '1501179691627-eeaa65ea017c'], ['Pulau Kelapa', '1537996194471-e657df975ab4']] as [$name, $photo]) {
                $pages[] = ['destination', 'destinasi-'.Str::slug($name), $name, 'Kab. Administrasi Kepulauan Seribu', $image($photo)];
            }
            foreach ([
                ['Ayam Betutu Gilimanuk', '1537996194471-e657df975ab4', 'Cita rasa khas rempah base genep Bali dengan ayam empuk beraroma sedap.'],
                ['Gudeg Yu Djum & Kopi Jos', '1584810359583-96fc3448beaa', 'Kuliner legendaris nangka manis gurih khas Yogyakarta dipadu sensasi arang membara.'],
                ['Ayam Taliwang & Plecing Kangkung', '1518548419970-58e3b4079ab2', 'Kelezatan pedas gurih khas Lombok dengan sambal terasi bakar yang autentik.'],
                ['Seafood Segar & Ikan Kuah Asam Labuan Bajo', '1516690561799-46d8f74f9abf', 'Hasil tangkapan segar nelayan Flores dengan bumbu asam segar rempah nusantara.'],
                ['Coto Makassar & Konro Bakar', '1544551763-46a013bb70d5', 'Kuah rempah kacang kental khas tanah Daeng dengan iga bakar empuk menggoda.'],
                ['Rawon Daging Sapi Kluwek', '1588668214407-6ea9a6d8c272', 'Sup daging berkuah hitam pekat dari rempah kluwek khas Jawa Timur yang gurih legendaris.'],
            ] as [$name, $photo, $desc]) {
                $pages[] = ['culinary', 'kuliner-'.Str::slug($name), $name, $desc, $image($photo)];
            }
            foreach ([
                ['Danau Paisu Pok Banggai', '1507525428034-b723cf961d3e', 'Danau air tawar sejernih kaca di pedalaman Banggai Kepulauan, Sulawesi Tengah.'],
                ['Desa Adat Wae Rebo', '1516690561799-46d8f74f9abf', 'Desa di atas awan dengan 7 rumah kerucut Mbaru Niang di pedalaman Manggarai Barat, Flores.'],
                ['Air Terjun Tumpak Sewu', '1588668214407-6ea9a6d8c272', 'Tirai air terjun spektakuler mirip Niagara di kaki Gunung Semeru, Lumajang.'],
                ['Bukit Ollon Tana Toraja', '1544551763-46a013bb70d5', 'Hamparan bukit teletubbies dan sungai jernih di lembah terpencil Toraja.'],
            ] as [$name, $photo, $desc]) {
                $pages[] = ['hidden-gem', 'hidden-gem-'.Str::slug($name), $name, $desc, $image($photo)];
            }
            foreach ([
                ['Kain Tenun Ikat Asli Flores', '1516690561799-46d8f74f9abf', 'Karya seni tenun tangan masyarakat lokal dengan pewarna alami rempah nusantara.'],
                ['Kopi Arabika Toraja & Flores Bajawa', '1544551763-46a013bb70d5', 'Biji kopi pilihan kualitas ekspor dari dataran tinggi vulkanik Indonesia.'],
                ['Batik Tulis Tradisional Yogyakarta', '1584810359583-96fc3448beaa', 'Kain batik dengan motif klasik filosofis buatan perajin desa wisata lokal.'],
                ['Pie Susu & Kopi Bali Asli', '1537996194471-e657df975ab4', 'Camilan renyah manis dan aroma kopi khas Pulau Dewata untuk buah tangan terbaik.'],
            ] as [$name, $photo, $desc]) {
                $pages[] = ['souvenir', 'souvenir-'.Str::slug($name), $name, $desc, $image($photo)];
            }
            foreach ($pages as $position => [$type, $slug, $title, $excerpt, $photo]) {
                $this->seedRecord(ContentPage::class, 'page:'.$slug, ['slug' => $slug], ['type' => $type, 'title' => $title, 'excerpt' => $excerpt, 'body' => $excerpt, 'image_url' => $photo, 'status' => 'published', 'position' => $position, 'published_at' => now()]);
            }
            foreach ($source['trips'] as $index => $item) {
                $this->seedRecord(Trip::class, 'trip:'.$item['id'], ['slug' => $item['id']], ['vendor_id' => $vendor->id, 'title' => $item['name'], 'type' => 'open-trip', 'destination' => $item['destination'], 'description' => 'Paket contoh '.$item['name'].'. '.$item['duration'].' bersama partner lokal. Detail fasilitas dan jadwal dapat diperbarui oleh vendor.', 'itinerary' => "Hari 1: registrasi, briefing dan jelajah destinasi.\nHari 2: kegiatan bersama pemandu lokal.\nHari terakhir: persiapan pulang dan perjalanan kembali.", 'meeting_point' => 'Titik kumpul '.$item['location'], 'image_url' => $item['image'], 'departure_date' => today()->addDays(14 + $index), 'end_date' => today()->addDays(16 + $index), 'capacity' => 30, 'price' => (int) preg_replace('/\D/', '', $item['price']), 'status' => 'published']);
            }
            foreach (['open-trip' => 'pulau-pramuka', 'private-trip' => 'labuan-bajo'] as $type => $slug) {
                $data = $source['details'][$type];
                $detail = $data['detail'];
                $this->seedRecord(Trip::class, 'trip:'.$slug, ['slug' => $slug], ['vendor_id' => $vendor->id, 'title' => $detail['title'], 'type' => $type, 'destination' => $detail['location'], 'description' => $detail['subtitle'], 'itinerary' => collect($data['itineraryDays'])->map(fn ($day) => $day['day']."\n".implode("\n", $day['activities']))->implode("\n\n"), 'meeting_point' => $detail['startPoint'], 'image_url' => $detail['images'][0], 'departure_date' => today()->addDays(14), 'end_date' => today()->addDays($type === 'open-trip' ? 15 : 16), 'capacity' => 30, 'price' => (int) preg_replace('/\D/', '', $detail['price']), 'status' => 'published', 'experience' => $data]);
            }
            foreach ($source['articles'] as $position => $article) {
                $this->seedRecord(ContentPage::class, 'article:'.$article['id'], ['slug' => $article['id']], ['type' => 'blog', 'title' => $article['title'], 'excerpt' => $article['excerpt'], 'body' => $article['body'], 'category' => $article['category'], 'metadata' => $article, 'image_url' => $image($article['image']), 'status' => 'published', 'position' => $position, 'published_at' => now()]);
            }
            foreach ($source['faqs'] as $position => $faq) {
                $this->seedRecord(Faq::class, 'faq:'.$faq['id'], ['question' => $faq['question']], ['answer' => $faq['answer'], 'category' => 'Perjalanan', 'status' => 'published', 'position' => $position]);
            }
            foreach ($source['partners'] as $position => $partner) {
                $this->seedRecord(Partner::class, 'partner:'.$position, ['name' => $partner['name']], ['image_url' => $partner['image'], 'status' => 'published', 'position' => $position]);
            }
            foreach ($source['reviews'] as $position => $review) {
                $this->seedRecord(ContentPage::class, 'testimonial:'.$position, ['slug' => 'testimoni-contoh-'.$position], ['type' => 'testimonial', 'title' => $review['name'], 'excerpt' => $review['trip'], 'body' => $review['quote'], 'metadata' => ['highlight' => $review['highlight'], 'demo' => true], 'status' => 'published', 'position' => $position, 'published_at' => now()]);
            }
            foreach (VirtualTour::whereNotNull('source_hash')->whereNull('seed_key')->get() as $tour) {
                $edited = AuditLog::where('entity_type', 'VirtualTour')->where('entity_id', $tour->id)->where('action', 'virtual_tour.saved')->exists();
                $tour->forceFill(['seed_key' => 'imported:'.$tour->id, ...(! $edited && $tour->status === 'draft' ? ['status' => 'published', 'placement' => 'homepage'] : [])])->save();
            }
            $audit->record('website.seeded', $vendor, ['source' => 'Desain website sebelum integrasi backend'], $owner->id);

            return $credentials ?? null;
        });
        if ($credentials && ! app()->environment('testing')) {
            $path = 'bootstrap/vendor-demo-credentials-'.now()->format('Ymd-His').'.json';
            Storage::disk('local')->put($path, json_encode($credentials, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->command?->info('Kredensial vendor demo tersimpan di: '.Storage::disk('local')->path($path));
        }
        $this->command?->info('Konten desain lama tersimpan. Data yang pernah dibuat, diedit, atau dihapus tidak ditimpa.');
    }

    /** @param class-string<Model> $model */
    private function seedRecord(string $model, string $key, array $match, array $data): void
    {
        if ($model::withTrashed()->where('seed_key', $key)->exists()) {
            return;
        }
        $record = $model::withTrashed()->firstOrCreate($match, $data);
        $record->forceFill(['seed_key' => $key])->save();
    }
}
