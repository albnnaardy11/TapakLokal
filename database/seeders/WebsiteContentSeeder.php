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
                throw new \RuntimeException('Email vendor contoh sudah dipakai akun lain.');
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
            foreach ([['Yogyakarta', '1584810359583-96fc3448beaa'], ['Bali', '1537996194471-e657df975ab4'], ['Lombok', '1518548419970-58e3b4079ab2'], ['Kepulauan Seribu', '1516690561799-46d8f74f9abf'], ['Jawa Barat', '1501179691627-eeaa65ea017c'], ['Pulau Komodo', '1518548419970-58e3b4079ab2'], ['Raja Ampat', '1516690561799-46d8f74f9abf']] as [$name, $photo]) {
                $pages[] = ['destination', 'destinasi-'.Str::slug($name), $name, 'Jelajahi keindahan '.$name.' bersama masyarakat dan pemandu lokal.', $image($photo)];
            }
            foreach ([['Pulau Pramuka', '1516690561799-46d8f74f9abf'], ['Pulau Pari', '1518548419970-58e3b4079ab2'], ['Pulau Tidung', '1546026423-cc4642628d2b'], ['Pulau Harapan', '1501179691627-eeaa65ea017c'], ['Pulau Kelapa', '1537996194471-e657df975ab4']] as [$name, $photo]) {
                $pages[] = ['destination', 'destinasi-'.Str::slug($name), $name, 'Kab. Administrasi Kepulauan Seribu', $image($photo)];
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
