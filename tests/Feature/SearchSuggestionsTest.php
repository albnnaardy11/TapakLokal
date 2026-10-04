<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchSuggestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_suggestions_returns_results_for_indonesian_travel_abbreviations(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'name' => 'Gunung Indo Adventure',
            'city' => 'Bogor',
            'email' => 'vendor.salak@example.com',
            'phone' => '081234567890',
            'status' => 'verified',
        ]);

        $trip = Trip::create([
            'vendor_id' => $vendor->id,
            'title' => 'Trekking Gunung Salak & Curug Rimba',
            'slug' => 'trekking-gunung-salak-curug-rimba',
            'type' => 'open-trip',
            'destination' => 'Gunung Salak, Bogor',
            'description' => 'Petualangan rimba Gunung Salak dan air terjun alami',
            'itinerary' => 'Hari 1: Pos Salak',
            'meeting_point' => 'Pos Registrasi Gunung Salak',
            'departure_date' => today()->addDays(7),
            'end_date' => today()->addDays(8),
            'capacity' => 20,
            'price' => 350000,
            'status' => 'published',
        ]);

        $destination = ContentPage::create([
            'type' => 'destination',
            'slug' => 'destinasi-gunung-salak',
            'title' => 'Gunung Salak',
            'excerpt' => 'Keindahan jalur rimba dan curug Gunung Salak',
            'body' => 'Pesona alam pegunungan Salak',
            'status' => 'published',
        ]);

        // Search with abbreviation "gn salak"
        $response = $this->getJson(route('search.suggestions', ['q' => 'gn salak']));
        $response->assertOk();
        $data = $response->json();

        $this->assertNotEmpty($data['trips']);
        $this->assertEquals($trip->id, $data['trips'][0]['id']);
        $this->assertStringContainsString('Gunung Salak', $data['trips'][0]['title']);

        $this->assertNotEmpty($data['destinations']);
        $this->assertEquals($destination->id, $data['destinations'][0]['id']);

        // Test catalog search with abbreviation
        $catalogResponse = $this->get(route('catalog', ['q' => 'gn salak']));
        $catalogResponse->assertOk();

        // Test category search with abbreviation
        $categoryResponse = $this->get(route('trips.category', ['type' => 'open-trip', 'q' => 'gn salak', 'guests' => 2]));
        $categoryResponse->assertOk();
        $categoryResponse->assertInertia(fn ($page) => $page
            ->component('TripCategory')
            ->has('trips.data', 1)
            ->where('trips.data.0.title', 'Trekking Gunung Salak & Curug Rimba')
            ->where('trips.data.0.slug', 'trekking-gunung-salak-curug-rimba')
        );

        // Test category search with full title "Gunung Salak"
        $categoryFullResponse = $this->get(route('trips.category', ['type' => 'open-trip', 'q' => 'Gunung Salak', 'guests' => 2]));
        $categoryFullResponse->assertOk();
        $categoryFullResponse->assertInertia(fn ($page) => $page
            ->component('TripCategory')
            ->has('trips.data', 1)
            ->where('trips.data.0.title', 'Trekking Gunung Salak & Curug Rimba')
        );
    }
}
