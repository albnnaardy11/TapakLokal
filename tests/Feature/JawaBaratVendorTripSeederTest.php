<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\Trip;
use App\Models\Vendor;
use Database\Seeders\JawaBaratVendorTripSeeder;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class JawaBaratVendorTripSeederTest extends TestCase
{
    public function test_it_creates_real_vendor_owned_catalog_packages_with_complete_bromo_content(): void
    {
        Storage::fake('public');

        $this->seed(JawaBaratVendorTripSeeder::class);

        $this->assertDatabaseCount('vendors', 10);
        $this->assertDatabaseCount('trips', 20);
        $this->assertDatabaseCount('reviews', 16);

        $bromo = Trip::query()->with('vendor.user')->where('slug', 'bromo')->firstOrFail();

        $this->assertSame('open-trip', $bromo->type);
        $this->assertSame('Puncak Hijau Trip', $bromo->vendor->name);
        $this->assertTrue($bromo->vendor->user->hasPermission('vendor.access'));
        $this->assertCount(5, $bromo->experience['detail']['images']);
        $this->assertCount(3, $bromo->experience['highlights']);
        $this->assertCount(3, $bromo->experience['destinations']);
        $this->assertCount(3, $bromo->experience['faqs']);
        Storage::disk('public')->assertExists('trips/catalog/puncak-hijau-bromo-1.webp');

        $this->get('/trips/open-trip/bromo')->assertInertia(fn (Assert $page) => $page
            ->where('tripData.slug', 'bromo')
            ->where('tripData.vendor.name', 'Puncak Hijau Trip')
            ->has('tripData.experience.detail.images', 5)
            ->has('tripData.experience.itineraryDays', 3));

        $this->actingAs($bromo->vendor->user)
            ->get('/vendor/trips')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->has('records.data', 2));

        $favoriteVendorIds = Vendor::query()->whereIn('name', ['Salak Rimba Adventure', 'Gede Pangrango Explorer'])->pluck('id');
        $this->assertSame(16, Review::query()->whereIn('trip_id', Trip::query()->whereIn('vendor_id', $favoriteVendorIds)->pluck('id'))->count());
    }
}
