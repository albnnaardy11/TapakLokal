<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use App\Models\User;
use App\Services\AccessService;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    public static function panels(): array
    {
        return [['content_admin', 'content', 'finance'], ['operations_admin', 'operations', 'growth'], ['finance_admin', 'finance', 'content'], ['growth_admin', 'growth', 'operations']];
    }

    #[DataProvider('panels')]
    public function test_each_admin_has_an_authorized_panel_and_cannot_open_another(string $role, string $panel, string $forbidden): void
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, $role);
        $this->actingAs($user)->get('/admin')->assertRedirect('/admin/'.$panel);
        $this->get('/admin/'.$panel)->assertInertia(fn (Assert $page) => $page->component('Admin/Dashboard')->where('adminPanel.key', $panel)->has('adminPanel.available', 1));
        $this->get('/admin/'.$forbidden)->assertForbidden();
    }

    public function test_super_admin_can_switch_between_all_panels_and_defaults_to_reports(): void
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, 'super_admin');
        $this->actingAs($user)->get('/admin')->assertRedirect('/admin/super');
        foreach (['super', 'content', 'operations', 'finance', 'growth'] as $panel) {
            $this->get('/admin/'.$panel)->assertInertia(fn (Assert $page) => $page->where('adminPanel.key', $panel)->has('adminPanel.available', 5));
        }
    }

    public function test_changing_panel_in_a_resource_url_does_not_bypass_module_permissions(): void
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, 'content_admin');
        $this->actingAs($user)->post('/admin/content/modules/payments', [])->assertNotFound();
        $this->get('/admin/finance/modules/payments')->assertForbidden();
    }

    public function test_content_panel_links_and_saved_content_stay_in_content_panel(): void
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, 'content_admin');
        $this->actingAs($user)->get('/admin/content/modules/blog')->assertInertia(fn (Assert $page) => $page->where('adminPanel.key', 'content'));
        $this->post('/admin/content/modules/blog', ['title' => 'Cerita Lokal', 'slug' => 'panel-content-test', 'body' => 'Cerita untuk traveler.', 'status' => 'draft', 'position' => 0])->assertRedirect('/admin/content/modules/blog/'.ContentPage::firstOrFail()->id);
    }
}
