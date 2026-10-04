<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Trip;
use App\Models\Vendor;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TripCatalogDataTest extends TestCase
{
    public function test_catalog_returns_vendor_gallery_and_published_review_aggregates(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        $trip = Trip::factory()->create([
            'vendor_id' => $vendor->id,
            'title' => 'Katalog Gunung Salak',
            'slug' => 'katalog-gunung-salak',
            'destination' => 'Gunung Salak, Bogor',
            'meeting_point' => 'Pos Registrasi Gunung Salak',
            'experience' => ['detail' => ['images' => ['https://example.test/salak-1.webp', 'https://example.test/salak-2.webp']]],
        ]);
        $booking = Booking::factory()->create([
            'trip_id' => $trip->id,
            'vendor_id' => $vendor->id,
            'status' => 'completed',
        ]);
        Review::factory()->create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'trip_id' => $trip->id,
            'rating' => 5,
            'status' => 'published',
        ]);
        $secondBooking = Booking::factory()->create([
            'trip_id' => $trip->id,
            'vendor_id' => $vendor->id,
            'status' => 'completed',
        ]);
        Review::factory()->create([
            'booking_id' => $secondBooking->id,
            'user_id' => $secondBooking->user_id,
            'trip_id' => $trip->id,
            'rating' => 4,
            'status' => 'published',
        ]);

        $this->get('/pilihan-trip/open-trip?q=Katalog')
            ->assertInertia(fn (Assert $page) => $page
                ->component('TripCategory')
                ->has('trips.data', 1)
                ->where('trips.data.0.slug', 'katalog-gunung-salak')
                ->where('trips.data.0.reviews_avg_rating', 4.5)
                ->where('trips.data.0.reviews_count', 2)
                ->where('trips.data.0.experience.detail.images.0', 'https://example.test/salak-1.webp'));
    }
}
