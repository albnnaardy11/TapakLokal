<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use App\Models\User;
use App\Services\AccessService;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PlatformContentTest extends TestCase
{
    public function test_content_admin_can_publish_content_and_it_is_visible_publicly(): void
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, 'content_admin');
        $this->actingAs($user)->post('/admin/modules/blog', ['title' => 'Cerita Desa', 'slug' => 'cerita-desa', 'body' => 'Cerita perjalanan masyarakat desa.', 'status' => 'published', 'position' => 0])->assertRedirect();
        $this->assertDatabaseHas('content_pages', ['slug' => 'cerita-desa', 'type' => 'blog', 'status' => 'published']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'record.created', 'entity_type' => 'ContentPage']);
        $this->get('/blog/cerita-desa')->assertInertia(fn (Assert $page) => $page->component('Content')->where('content.title', 'Cerita Desa'));
    }

    public function test_drafts_and_scheduled_content_are_not_publicly_accessible(): void
    {
        $this->freezeTime();
        $draft = ContentPage::factory()->create();
        $scheduled = ContentPage::factory()->create(['status' => 'published', 'published_at' => now()->addDay()]);
        $this->get('/blog/'.$draft->slug)->assertNotFound();
        $this->get('/pages/'.$scheduled->slug)->assertNotFound();
        $this->get('/blog')->assertInertia(fn (Assert $page) => $page->has('publishedArticles.data', 0));
    }

    public function test_finance_admin_cannot_modify_content(): void
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, 'finance_admin');
        $this->actingAs($user)->post('/admin/modules/blog', ['title' => 'Unauthorized'])->assertForbidden();
        $this->assertDatabaseCount('content_pages', 0);
    }

    public function test_content_record_cannot_be_modified_through_a_different_module(): void
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, 'content_admin');
        $page = ContentPage::factory()->create(['type' => 'page']);
        $this->actingAs($user)->get('/admin/modules/blog/'.$page->id)->assertNotFound();
    }

    public function test_homepage_destinations_come_from_published_cms_records(): void
    {
        ContentPage::factory()->create(['type' => 'destination', 'status' => 'published', 'published_at' => now()->subDay(), 'title' => 'Curug Cidaun']);
        ContentPage::factory()->create(['type' => 'destination', 'status' => 'draft']);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->has('cmsDestinations', 1)->where('cmsDestinations.0.title', 'Curug Cidaun'));
    }
}
