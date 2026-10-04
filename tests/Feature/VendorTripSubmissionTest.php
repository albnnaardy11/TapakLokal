<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AccessService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class VendorTripSubmissionTest extends TestCase
{
    public function test_submission_notifies_operations_and_can_be_published_by_admin(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');
        $admin = User::factory()->create();
        app(AccessService::class)->grant($admin, 'operations_admin');
        $outsider = User::factory()->create();
        $payload = Trip::factory()->make(['vendor_id' => $vendor->id, 'status' => 'draft'])->only(['title', 'slug', 'type', 'destination', 'description', 'itinerary', 'meeting_point', 'departure_date', 'end_date', 'capacity', 'price', 'status']);

        $this->actingAs($vendor->user)->post('/vendor/trips', $payload)->assertSessionHasNoErrors()->assertRedirect('/vendor/trips');
        $trip = Trip::where('slug', $payload['slug'])->firstOrFail();
        $this->assertSame(0, $admin->notifications()->count());
        $this->put('/vendor/trips/'.$trip->id, [...$payload, 'status' => 'pending', 'experience' => ['detail' => ['images' => array_map(fn ($index) => 'https://example.com/trip-'.$index.'.jpg', range(1, 5))]]])->assertSessionHasNoErrors()->assertRedirect('/vendor/trips');
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'status' => 'pending', 'vendor_id' => $vendor->id]);
        $this->assertSame('trip.submitted', $admin->notifications()->firstOrFail()->type);
        $this->assertSame(0, $outsider->notifications()->count());
        $this->actingAs($admin)->get('/admin/operations/modules/trips/'.$trip->id)->assertOk();
        $this->post('/admin/operations/modules/trips/'.$trip->id.'/publish')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'status' => 'published']);
        $this->assertSame('Trip Anda telah diterbitkan', $vendor->user->notifications()->firstOrFail()->data['title']);
    }

    public function test_search_and_counts_remain_scoped_to_the_vendor(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');
        Trip::factory()->create(['vendor_id' => $vendor->id, 'title' => 'Jelajah Bogor', 'status' => 'pending']);
        Trip::factory()->create(['vendor_id' => $vendor->id, 'title' => 'Jelajah Bandung', 'status' => 'draft']);
        Trip::factory()->create(['title' => 'Jelajah Bogor', 'status' => 'pending']);

        $this->actingAs($vendor->user)->get('/vendor/trips?search=Bogor&status=pending')->assertInertia(fn (Assert $page) => $page
            ->has('records.data', 1)->where('records.data.0.title', 'Jelajah Bogor')
            ->where('tripCounts.pending', 1)->where('tripCounts.draft', 1));
    }

    public function test_trip_slug_is_auto_generated_from_title_when_blank_or_unformatted(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');

        $payload = Trip::factory()->make([
            'vendor_id' => $vendor->id,
            'title' => 'Open Trip Gn Salak',
            'slug' => '',
            'status' => 'draft',
        ])->only(['title', 'slug', 'type', 'destination', 'description', 'itinerary', 'meeting_point', 'departure_date', 'end_date', 'capacity', 'price', 'status']);

        $this->actingAs($vendor->user)->post('/vendor/trips', $payload)->assertSessionHasNoErrors()->assertRedirect('/vendor/trips');
        $this->assertDatabaseHas('trips', [
            'vendor_id' => $vendor->id,
            'title' => 'Open Trip Gn Salak',
            'slug' => 'open-trip-gn-salak',
        ]);
    }

    public function test_vendor_can_upload_and_optimize_image_to_webp(): void
    {
        Storage::fake('public');
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');

        $file = UploadedFile::fake()->image('gunung-salak.jpg', 1200, 800);

        $response = $this->actingAs($vendor->user)->postJson('/vendor/trips/upload-image', [
            'image' => $file,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('mime_type', 'image/webp');

        $this->assertStringEndsWith('.webp', $response->json('filename'));
        Storage::disk('public')->assertExists('trips/'.$response->json('filename'));
    }

    public function test_vendor_can_delete_trip_without_bookings(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');
        $trip = Trip::factory()->create(['vendor_id' => $vendor->id, 'title' => 'Trip Hapus']);

        $response = $this->actingAs($vendor->user)->delete('/vendor/trips/'.$trip->id);
        $response->assertSessionHasNoErrors()->assertRedirect('/vendor/trips');

        $this->assertSoftDeleted('trips', ['id' => $trip->id]);
    }
}
