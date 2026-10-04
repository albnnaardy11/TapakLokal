<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\Vendor;
use App\Services\AccessService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class VendorTripContentTest extends TestCase
{
    private function payload(Vendor $vendor): array
    {
        return Trip::factory()->make(['vendor_id' => $vendor->id, 'status' => 'pending'])
            ->only(['title', 'slug', 'type', 'destination', 'description', 'itinerary', 'meeting_point', 'departure_date', 'end_date', 'capacity', 'price', 'status']);
    }

    public function test_five_uploaded_photos_and_vendor_content_reach_the_public_detail(): void
    {
        Storage::fake('public');
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');
        $this->actingAs($vendor->user);
        $photos = [];
        for ($index = 0; $index < 5; $index++) {
            $response = $this->postJson('/vendor/trips/upload-image', ['image' => UploadedFile::fake()->image('trip-'.$index.'.jpg', 400, 300)]);
            $response->assertOk();
            $photos[] = $response->json('url');
            Storage::disk('public')->assertExists('trips/'.$response->json('filename'));
        }
        $content = [
            'detail' => ['images' => $photos, 'subtitle' => 'Jelajah bersama pemandu lokal.', 'coordinates' => '-6.2,106.8', 'meetingTime' => '06.00 WIB', 'arrivalNote' => 'Datang 30 menit lebih awal', 'meetingNote' => 'Temui pemandu di gerbang'],
            'description_html' => '<h3>Petualangan Salak</h3><p><strong>Terbuka</strong> untuk peserta baru.</p>',
            'itinerary_html' => '<p>Registrasi pagi hari.</p><ul><li>Briefing</li><li>Pendakian</li></ul>',
            'highlights' => [['title' => 'Puncak Salak', 'description' => 'Melihat panorama pegunungan.', 'time' => 'Hari 1, 10.00', 'location' => 'Bogor', 'images' => [$photos[1], $photos[2]], 'icon' => 'Compass']],
            'destinations' => [['name' => 'Pos pendakian', 'subtitle' => 'Awal perjalanan', 'coordinates' => '-6.2,106.8', 'image_url' => $photos[2], 'time' => '07.00', 'activity' => 'Registrasi', 'note' => 'Bawa identitas']],
            'itineraryDays' => [['day' => 'Hari 1', 'meals' => 'Makan siang', 'activities' => ['07.00 Registrasi', '08.00 Berangkat']]],
            'facilityDetails' => [['title' => 'Pemandu', 'category' => 'Layanan', 'note' => 'Pemandu lokal']],
            'included' => ['Tiket masuk'], 'excluded' => ['Transportasi ke titik kumpul'], 'packingItems' => ['Sepatu gunung'],
            'faqs' => [['question' => 'Bisa untuk pemula?', 'answer' => 'Bisa dengan kondisi sehat.']],
            'facilities' => [['label' => 'Pemandu lokal', 'icon' => 'Users']],
            'panoramas' => [['title' => 'Puncak', 'label' => 'Panorama pegunungan', 'image_url' => $photos[3]]],
        ];
        $payload = [...$this->payload($vendor), 'experience' => $content];

        $this->post('/vendor/trips', $payload)->assertSessionHasNoErrors()->assertRedirect('/vendor/trips');
        $trip = Trip::where('slug', $payload['slug'])->firstOrFail();
        $this->assertSame($photos[0], $trip->image_url);
        $this->assertSame(Arr::sortRecursive($content), Arr::sortRecursive($trip->experience));
        $this->get('/vendor/trips/'.$trip->id.'/edit')->assertInertia(fn (Assert $page) => $page->where('trip.experience.detail.images', $photos)->where('trip.experience.faqs.0.question', 'Bisa untuk pemula?'));
        $trip->update(['status' => 'published']);
        $this->get('/trips/'.$trip->type.'/'.$trip->slug)->assertInertia(fn (Assert $page) => $page
            ->where('tripData.experience.detail.images', $photos)->where('tripData.experience.itineraryDays.0.day', 'Hari 1')->has('reviews', 0)->has('relatedTrips', 0)
            ->where('tripData.description', "Petualangan Salak\nTerbuka untuk peserta baru."));
    }

    public function test_submission_requires_five_distinct_photos_but_draft_can_be_incomplete(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');
        $payload = $this->payload($vendor);
        $this->actingAs($vendor->user)->post('/vendor/trips', $payload)->assertSessionHasErrors('experience.detail.images');
        $this->assertDatabaseCount('trips', 0);
        $this->post('/vendor/trips', [...$payload, 'experience' => ['detail' => ['images' => array_map(fn ($index) => 'https://example.com/'.$index.'.jpg', range(1, 4))]]])->assertSessionHasErrors('experience.detail.images');
        $this->post('/vendor/trips', [...$payload, 'experience' => ['detail' => ['images' => array_fill(0, 5, 'https://example.com/same.jpg')]]])->assertSessionHasErrors('experience.detail.images.0');
        $this->assertDatabaseCount('trips', 0);
        $this->post('/vendor/trips', [...$payload, 'status' => 'draft'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('trips', ['slug' => $payload['slug'], 'status' => 'draft']);
    }

    public function test_rich_text_keeps_formatting_and_removes_executable_markup(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');
        $payload = [...$this->payload($vendor), 'status' => 'draft', 'experience' => [
            'description_html' => '<p onclick="alert(1)"><strong>Selamat datang</strong><script>alert(1)</script><img src=x onerror=alert(1)><a href="javascript:alert(1)">jelajah</a></p>',
            'itinerary_html' => '<ul><li>Registrasi</li></ul>',
        ]];

        $this->actingAs($vendor->user)->post('/vendor/trips', $payload)->assertSessionHasNoErrors();
        $trip = Trip::where('slug', $payload['slug'])->firstOrFail();
        $this->assertSame('<p><strong>Selamat datang</strong>jelajah</p>', $trip->experience['description_html']);
        $this->assertSame('Selamat datangjelajah', $trip->description);
        $this->assertSame('<ul><li>Registrasi</li></ul>', $trip->experience['itinerary_html']);
    }

    public function test_invalid_nested_content_and_image_protocol_are_rejected(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');
        $payload = [...$this->payload($vendor), 'status' => 'draft', 'experience' => [
            'detail' => ['images' => ['javascript:alert(1)']],
            'faqs' => [['question' => 'Syarat?', 'answer' => ['invalid']]],
            'itineraryDays' => [['day' => 'Hari 1', 'activities' => 'invalid']],
        ]];

        $this->actingAs($vendor->user)->post('/vendor/trips', $payload)->assertSessionHasErrors(['experience.detail.images.0', 'experience.faqs.0.answer', 'experience.itineraryDays.0.activities']);
        $this->assertDatabaseCount('trips', 0);
    }
}
