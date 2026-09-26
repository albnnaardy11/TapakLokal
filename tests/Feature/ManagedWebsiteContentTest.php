<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use App\Models\Trip;
use App\Models\User;
use App\Services\AccessService;
use Database\Seeders\WebsiteContentSeeder;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ManagedWebsiteContentTest extends TestCase
{
    public function test_seeded_website_content_restores_homepage_sections_and_real_catalog_data(): void
    {
        $this->seed(WebsiteContentSeeder::class);

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->has('featuredTrips', 6)
            ->has('cmsDestinations', 7)
            ->has('cmsArticles', 5)
            ->has('cmsFaqs', 6)
            ->has('cmsPartners', 6)
            ->has('cmsTestimonials', 4)
            ->has('virtualTours', 0));

        $trip = Trip::where('slug', 'pulau-pramuka')->firstOrFail();
        $this->get('/trips/open-trip/pulau-pramuka')->assertInertia(fn (Assert $page) => $page
            ->where('tripData.title', 'Open Trip Tur Pulau Pramuka')
            ->has('tripData.experience'));
    }

    public function test_deleted_seed_content_stays_deleted_after_seeder_runs_again(): void
    {
        $this->seed(WebsiteContentSeeder::class);
        $article = ContentPage::where('slug', 'bali')->firstOrFail();
        $article->delete();

        $this->seed(WebsiteContentSeeder::class);

        $this->assertSoftDeleted('content_pages', ['id' => $article->id]);
        $this->get('/blog/bali')->assertNotFound();
    }

    public function test_content_and_vendor_can_remove_seeded_data_they_own_when_no_booking_exists(): void
    {
        $this->seed(WebsiteContentSeeder::class);
        $contentAdmin = User::factory()->create();
        app(AccessService::class)->grant($contentAdmin, 'content_admin');
        $article = ContentPage::where('slug', 'bali')->firstOrFail();
        $this->actingAs($contentAdmin)->delete('/admin/content/modules/blog/'.$article->id)->assertRedirect('/admin/content/modules/blog');
        $this->assertSoftDeleted('content_pages', ['id' => $article->id]);

        $vendor = User::where('email', config('demo.vendor_email'))->firstOrFail();
        $trip = Trip::where('slug', 'pulau-pramuka')->firstOrFail();
        $this->actingAs($vendor)->delete('/vendor/trips/'.$trip->id)->assertRedirect('/vendor/trips');
        $this->assertSoftDeleted('trips', ['id' => $trip->id]);
    }
}
