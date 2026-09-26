<?php

namespace Tests\Feature;

use App\Jobs\ReconcilePayment;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Trip;
use App\Models\User;
use App\Services\BookingService;
use App\Services\FinanceService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    private function booking(): Booking
    {
        $this->freezeTime();
        config(['platform.midtrans_server_key' => 'test-secret', 'platform.midtrans_production' => false]);

        return app(BookingService::class)->create(User::factory()->create(), ['trip_id' => Trip::factory()->create()->id, 'participants' => 1, 'contact_name' => 'Traveler', 'contact_phone' => '08123456789', 'idempotency_key' => (string) Str::uuid()]);
    }

    public function test_checkout_calls_gateway_outside_database_transaction_and_reuses_the_link(): void
    {
        $booking = $this->booking();
        $transactionLevel = DB::transactionLevel();
        Http::preventStrayRequests();
        Http::fake(['https://app.sandbox.midtrans.com/snap/v1/transactions' => function ($request) use ($transactionLevel, $booking) {
            $this->assertSame($transactionLevel, DB::transactionLevel());
            $this->assertSame($booking->reference, $request['transaction_details']['order_id']);

            return Http::response(['redirect_url' => 'https://app.sandbox.midtrans.com/snap/test-checkout']);
        }]);
        $finance = app(FinanceService::class);
        $this->assertSame('https://app.sandbox.midtrans.com/snap/test-checkout', $finance->checkout($booking));
        $finance->checkout($booking);
        Http::assertSentCount(1);
        $this->assertDatabaseHas('payments', ['booking_id' => $booking->id, 'checkout_url' => 'https://app.sandbox.midtrans.com/snap/test-checkout']);
    }

    public function test_concurrent_checkout_does_not_call_the_gateway_twice(): void
    {
        $booking = $this->booking();
        Http::preventStrayRequests();
        Http::fake();
        $lock = Cache::lock('payment:checkout:'.$booking->id, 45);
        $lock->get();
        try {
            $this->actingAs($booking->user)->post('/bookings/'.$booking->id.'/checkout')->assertSessionHasErrors('payment');
            Http::assertNothingSent();
        } finally {
            $lock->release();
        }
    }

    public function test_checkout_gateway_failure_keeps_booking_and_releases_lock(): void
    {
        $booking = $this->booking();
        Http::preventStrayRequests();
        Http::fake(['https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([], 503)]);
        $this->actingAs($booking->user)->post('/bookings/'.$booking->id.'/checkout')->assertSessionHasErrors('payment');
        $this->assertDatabaseHas('payments', ['booking_id' => $booking->id, 'status' => 'pending', 'checkout_url' => null]);
        Http::assertSentCount(1);
        $lock = Cache::lock('payment:checkout:'.$booking->id, 45);
        $this->assertTrue($lock->get());
        $lock->release();
    }

    public function test_valid_webhook_queues_verification_without_trusting_supplied_status(): void
    {
        $booking = $this->booking();
        Queue::fake([ReconcilePayment::class]);
        Http::preventStrayRequests();
        Http::fake();
        $this->postJson('/payments/midtrans/notification', ['order_id' => $booking->reference, 'status_code' => '200', 'gross_amount' => '500000.00', 'transaction_status' => 'settlement', 'signature_key' => hash('sha512', $booking->reference.'200500000.00test-secret')])->assertAccepted();
        Queue::assertPushed(ReconcilePayment::class, fn ($job) => $job->reference === $booking->reference);
        Http::assertNothingSent();
        $this->assertDatabaseHas('payments', ['booking_id' => $booking->id, 'status' => 'pending']);
    }

    public function test_worker_verifies_remote_status_and_updates_payment(): void
    {
        $booking = $this->booking();
        Http::preventStrayRequests();
        Http::fake(['https://api.sandbox.midtrans.com/v2/'.$booking->reference.'/status' => Http::response(['order_id' => $booking->reference, 'transaction_status' => 'settlement', 'transaction_id' => 'gateway-worker-test', 'gross_amount' => '500000.00'])]);
        (new ReconcilePayment($booking->reference))->handle(app(FinanceService::class));
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'paid']);
        $this->assertDatabaseCount('ledger_entries', 2);
        Http::assertSentCount(1);
    }

    public function test_amount_mismatch_fails_job_without_accepting_payment(): void
    {
        $booking = $this->booking();
        Http::preventStrayRequests();
        Http::fake(['https://api.sandbox.midtrans.com/v2/'.$booking->reference.'/status' => Http::response(['order_id' => $booking->reference, 'transaction_status' => 'settlement', 'transaction_id' => 'wrong-amount', 'gross_amount' => '1.00'])]);
        $job = (new ReconcilePayment($booking->reference))->withFakeQueueInteractions();
        $job->handle(app(FinanceService::class));
        $job->assertFailedWith(ValidationException::class);
        $this->assertDatabaseHas('payments', ['booking_id' => $booking->id, 'status' => 'pending']);
        $this->assertDatabaseCount('ledger_entries', 0);
        Http::assertSentCount(1);
    }

    public function test_late_settlement_requires_reconciliation_and_releases_inventory(): void
    {
        $booking = $this->booking();
        $booking->update(['expires_at' => now()->subMinute()]);
        app(FinanceService::class)->applyStatus(['order_id' => $booking->reference, 'transaction_status' => 'settlement', 'transaction_id' => 'late-payment', 'gross_amount' => '500000.00']);
        $this->assertDatabaseHas('payments', ['booking_id' => $booking->id, 'status' => 'reconciliation_required']);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'expired']);
        $this->assertDatabaseHas('trips', ['id' => $booking->trip_id, 'reserved_seats' => 0]);
    }

    public function test_scheduled_reconciliation_is_bounded_and_skips_paid_records(): void
    {
        $booking = $this->booking();
        $booking->payment()->update(['created_at' => now()->subMinutes(2)]);
        Payment::factory()->create(['status' => 'paid', 'created_at' => now()->subMinutes(2)]);
        Queue::fake([ReconcilePayment::class]);
        $this->artisan('payments:reconcile', ['--limit' => 1])->assertSuccessful();
        Queue::assertPushed(ReconcilePayment::class, 1);
        Queue::assertPushed(ReconcilePayment::class, fn ($job) => $job->reference === $booking->reference);
    }
}
