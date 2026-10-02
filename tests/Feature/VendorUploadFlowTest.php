<?php

namespace Tests\Feature;

use App\Models\MediaAsset;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AccessService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VendorUploadFlowTest extends TestCase
{
    public function test_verified_vendor_receives_product_photo_selection_after_upload(): void
    {
        Storage::fake('local');
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');
        $response = $this->actingAs($vendor->user)->from('/vendor/souvenirs')->post('/media', [
            'file' => UploadedFile::fake()->image('product.jpg'),
            'visibility' => 'public',
        ]);
        $media = MediaAsset::firstOrFail();
        $response->assertRedirect('/vendor/souvenirs')
            ->assertSessionHas('uploaded_media.id', $media->id)
            ->assertSessionHas('uploaded_media.url', '/media/'.$media->id)
            ->assertSessionHas('uploaded_media.visibility', 'public');
        Storage::disk('local')->assertExists($media->path);
        $this->assertSame($vendor->user_id, $media->user_id);
    }

    public function test_private_document_selection_does_not_make_verification_document_public(): void
    {
        Storage::fake('local');
        $vendor = Vendor::factory()->create(['status' => 'pending']);
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');
        $this->actingAs($vendor->user)->post('/media', [
            'file' => UploadedFile::fake()->create('verification.pdf', 10, 'application/pdf'),
            'visibility' => 'private',
        ])->assertSessionHas('uploaded_media.visibility', 'private');
        $media = MediaAsset::firstOrFail();
        $this->actingAs(User::factory()->create())->get('/media/'.$media->id)->assertNotFound();
        $this->assertSame('private', $media->visibility);
    }
}
