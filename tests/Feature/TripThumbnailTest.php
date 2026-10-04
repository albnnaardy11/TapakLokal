<?php

namespace Tests\Feature;

use App\Models\Trip;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TripThumbnailTest extends TestCase
{
    public function test_published_trip_exposes_a_compact_thumbnail_for_catalog_cards(): void
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('cover.jpg', 1200, 800);
        Storage::disk('public')->put('trips/cover.jpg', file_get_contents($image->getRealPath()));
        $trip = Trip::factory()->create(['image_url' => url('storage/trips/cover.jpg')]);

        $thumbnail = $this->get(route('trips.thumbnail', $trip))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/webp')
            ->streamedContent();

        $this->assertSame(480, getimagesizefromstring($thumbnail)[0]);
        $version = hash('sha256', 'trips/cover.jpg|'.Storage::disk('public')->lastModified('trips/cover.jpg').'|'.Storage::disk('public')->size('trips/cover.jpg'));
        $this->get('/')->assertInertia(fn ($page) => $page->where('featuredTrips.0.thumbnail_url', route('trips.thumbnail', ['trip' => $trip, 'v' => $version])));
    }
}
