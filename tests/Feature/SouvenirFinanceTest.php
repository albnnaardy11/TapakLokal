<?php

namespace Tests\Feature;

use App\Jobs\ReconcileSouvenirPayment;
use App\Models\SouvenirPayment;
use App\Models\User;
use App\Services\AccessService;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SouvenirFinanceTest extends TestCase
{
    public function test_finance_console_requires_finance_permission_and_lists_real_orders(): void
    {
        $payment = SouvenirPayment::factory()->create();
        $traveler = User::factory()->create();
        $this->actingAs($traveler)->get('/admin/keuangan-oleh-oleh')->assertForbidden();
        $finance = User::factory()->create();
        app(AccessService::class)->grant($finance, 'finance_admin');

        $this->actingAs($finance)->get('/admin/keuangan-oleh-oleh')->assertInertia(fn (Assert $page) => $page->component('Admin/SouvenirFinance')->has('orders.data', 1)->where('orders.data.0.payment.id', $payment->id));
    }

    public function test_reconciliation_queries_gateway_through_a_job_without_setting_paid_manually(): void
    {
        Queue::fake();
        config(['platform.midtrans_server_key' => 'test-secret']);
        $payment = SouvenirPayment::factory()->create();
        $finance = User::factory()->create();
        app(AccessService::class)->grant($finance, 'finance_admin');

        $this->actingAs($finance)->post('/admin/keuangan-oleh-oleh/'.$payment->id.'/rekonsiliasi')->assertRedirect();
        Queue::assertPushed(ReconcileSouvenirPayment::class, fn ($job) => $job->reference === $payment->reference);
        $this->assertDatabaseHas('souvenir_payments', ['id' => $payment->id, 'status' => 'pending']);
    }
}
