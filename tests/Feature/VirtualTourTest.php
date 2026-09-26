<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use App\Models\Trip;
use App\Models\User;
use App\Models\VirtualTour;
use App\Services\AccessService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class VirtualTourTest extends TestCase
{
    private function admin(string $role = 'content_admin'): User
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, $role);

        return $user;
    }

    private function payload(): array
    {
        return ['title' => 'Curug Panorama', 'slug' => 'curug-panorama', 'status' => 'draft', 'placement' => 'homepage', 'position' => 0];
    }

    public function test_content_admin_uploads_private_panorama_then_publishes_to_homepage(): void
    {
        Storage::fake('local');
        $this->actingAs($this->admin())->post('/admin/content/virtual-tours', [...$this->payload(), 'file' => UploadedFile::fake()->image('panorama.jpg', 1024, 512)])->assertSessionHasNoErrors()->assertRedirect('/admin/content/virtual-tours');
        $tour = VirtualTour::firstOrFail();
        $this->assertSame('private', $tour->media->visibility);
        Storage::disk('local')->assertExists($tour->media->path);
        $this->get('/admin/content/virtual-tours/'.$tour->id.'/preview')->assertOk()->assertHeader('Content-Type', 'image/webp');
        $this->get('/virtual-tours/'.$tour->id.'/image')->assertNotFound();
        $this->post('/admin/content/virtual-tours/'.$tour->id, [...$this->payload(), 'status' => 'published'])->assertSessionHasNoErrors()->assertRedirect();
        $this->get('/')->assertInertia(fn (Assert $page) => $page->has('virtualTours', 1)->where('virtualTours.0.title', 'Curug Panorama'));
        $this->get('/virtual-tours/'.$tour->id.'/image')->assertOk();
        $this->assertDatabaseHas('audit_logs', ['action' => 'virtual_tour.saved', 'entity_id' => $tour->id]);
    }

    public function test_finance_cannot_upload_preview_or_change_panorama_placement(): void
    {
        $tour = VirtualTour::factory()->create();
        $this->actingAs($this->admin('finance_admin'))->get('/admin/content/virtual-tours')->assertForbidden();
        $this->get('/admin/content/virtual-tours/'.$tour->id.'/preview')->assertForbidden();
        $this->post('/admin/content/virtual-tours/'.$tour->id, $this->payload())->assertForbidden();
        $this->get('/admin/content/virtual-tours/targets?type=trip')->assertForbidden();
    }

    public function test_flat_image_is_rejected_without_database_or_file_side_effects(): void
    {
        Storage::fake('local');
        $this->actingAs($this->admin())->post('/admin/content/virtual-tours', [...$this->payload(), 'file' => UploadedFile::fake()->image('flat.jpg', 1200, 800)])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('virtual_tours', 0);
        $this->assertDatabaseCount('media_assets', 0);
        $this->assertCount(0, Storage::disk('local')->allFiles());
    }

    public function test_tour_follows_target_visibility_and_moving_it_removes_old_placement(): void
    {
        Storage::fake('local');
        $trip = Trip::factory()->create();
        $tour = VirtualTour::factory()->create(['status' => 'published', 'placement' => 'trip', 'trip_id' => $trip->id]);
        Storage::disk('local')->put($tour->media->path, 'test');
        $this->get('/trips/'.$trip->type.'/'.$trip->slug)->assertInertia(fn (Assert $page) => $page->has('virtualTours', 1));
        $trip->vendor->update(['status' => 'suspended']);
        $this->get('/virtual-tours/'.$tour->id.'/image')->assertNotFound();
        $destination = ContentPage::factory()->create(['type' => 'destination', 'status' => 'published', 'published_at' => now()->subDay()]);
        $this->actingAs($this->admin())->post('/admin/content/virtual-tours/'.$tour->id, [...$this->payload(), 'status' => 'published', 'placement' => 'destination', 'content_page_id' => $destination->id, 'trip_id' => $trip->id])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertNull($tour->fresh()->trip_id);
        $this->get('/pages/'.$destination->slug)->assertInertia(fn (Assert $page) => $page->has('virtualTours', 1));
        $this->get('/')->assertInertia(fn (Assert $page) => $page->has('virtualTours', 0));
        $destination->update(['status' => 'draft']);
        $this->get('/virtual-tours/'.$tour->id.'/image')->assertNotFound();
    }

    public function test_import_command_is_idempotent_and_preserves_admin_edits(): void
    {
        Storage::fake('local');
        $owner = $this->admin();
        $file = UploadedFile::fake()->image('panorama.jpg', 1024, 512);
        $directory = Storage::disk('local')->path('source');
        mkdir($directory);
        copy($file->getRealPath(), $directory.'/panorama.jpg');
        $this->artisan('platform:import-tours', ['directory' => $directory, '--owner' => $owner->email])->assertSuccessful();
        $tour = VirtualTour::firstOrFail();
        $tour->update(['title' => 'Judul dikurasi', 'status' => 'published']);
        $this->artisan('platform:import-tours', ['directory' => $directory, '--owner' => $owner->email])->assertSuccessful();
        $this->assertDatabaseCount('virtual_tours', 1);
        $this->assertSame('Judul dikurasi', $tour->fresh()->title);
        $this->assertSame('published', $tour->fresh()->status);
    }

    public function test_cropped_panorama_uses_gpano_metadata_including_zero_offsets(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->image('cropped.jpg', 1200, 500);
        $bytes = file_get_contents($file->getRealPath());
        $metadata = 'http://ns.adobe.com/xap/1.0/'."\0".'<rdf:Description GPano:FullPanoWidthPixels="1200" GPano:FullPanoHeightPixels="600" GPano:CroppedAreaLeftPixels="0" GPano:CroppedAreaTopPixels="50" GPano:CroppedAreaImageWidthPixels="1200" GPano:CroppedAreaImageHeightPixels="500" />';
        file_put_contents($file->getRealPath(), substr($bytes, 0, 2)."\xff\xe1".pack('n', strlen($metadata) + 2).$metadata.substr($bytes, 2));
        $this->actingAs($this->admin())->post('/admin/content/virtual-tours', [...$this->payload(), 'file' => $file])->assertSessionHasNoErrors()->assertRedirect();
        $media = VirtualTour::firstOrFail()->media;
        $size = getimagesize(Storage::disk('local')->path($media->path));
        $this->assertSame([1200, 600], [$size[0], $size[1]]);
    }
}
