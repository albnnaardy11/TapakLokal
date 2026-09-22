<?php

namespace Tests\Feature;

use App\Models\{Booking, LedgerEntry, Payment, Promotion, Trip, User};
use App\Services\{BookingService, FinanceService};
use Illuminate\Support\Str;
use Tests\TestCase;

class PlatformWorkflowTest extends TestCase
{
    private function payload(Trip $trip, int $participants = 2): array
    {
        return ['trip_id' => $trip->id, 'participants' => $participants, 'contact_name' => 'Wisatawan', 'contact_phone' => '081234567890', 'idempotency_key' => (string) Str::uuid()];
    }

    public function test_booking_uses_server_price_and_duplicate_requests_reserve_seats_once(): void
    {
        $this->freezeTime();
        config(['platform.commission_bps' => 1000]);
        $trip = Trip::factory()->create(['price' => 250000]);
        $payload = [...$this->payload($trip), 'total' => 1, 'status' => 'paid'];
        $this->actingAs(User::factory()->create());
        $this->post('/bookings', $payload)->assertRedirect();
        $booking = Booking::firstOrFail();
        $this->post('/bookings', $payload)->assertRedirect('/bookings/'.$booking->id);
        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'total' => 500000, 'platform_fee' => 50000, 'vendor_amount' => 450000, 'status' => 'awaiting_payment']);
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'reserved_seats' => 2]);
        $this->assertDatabaseHas('payments', ['booking_id' => $booking->id, 'amount' => 500000, 'status' => 'pending']);
    }

    public function test_reusing_a_booking_key_with_different_input_is_rejected(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->payload($trip);
        $this->actingAs(User::factory()->create())->post('/bookings', $payload)->assertRedirect();
        $this->post('/bookings', [...$payload, 'participants' => 3])->assertSessionHasErrors('idempotency_key');
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'reserved_seats' => 2]);
    }

    public function test_insufficient_capacity_has_no_booking_or_payment_side_effects(): void
    {
        $trip = Trip::factory()->create(['capacity' => 1]);
        $this->actingAs(User::factory()->create())->post('/bookings', $this->payload($trip))->assertSessionHasErrors('participants');
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'reserved_seats' => 0]);
    }

    public function test_cancel_releases_seats_and_promotion_once(): void
    {
        $this->freezeTime();
        $promotion = Promotion::factory()->create(['code' => 'HEMAT', 'type' => 'percent', 'value' => 10, 'minimum_amount' => 0, 'maximum_discount' => 50000, 'status' => 'published']);
        $trip = Trip::factory()->create();
        $this->actingAs(User::factory()->create())->post('/bookings', [...$this->payload($trip), 'promotion_code' => 'hemat'])->assertRedirect();
        $booking = Booking::firstOrFail();
        $this->assertSame(950000, $booking->total);
        $this->post('/bookings/'.$booking->id.'/cancel')->assertRedirect();
        $this->post('/bookings/'.$booking->id.'/cancel')->assertRedirect();
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'reserved_seats' => 0]);
        $this->assertDatabaseHas('promotions', ['id' => $promotion->id, 'used_count' => 0]);
    }

    public function test_other_users_cannot_read_or_cancel_bookings(): void
    {
        $booking = Booking::factory()->create();
        $this->actingAs(User::factory()->create())->get('/bookings/'.$booking->id)->assertNotFound();
        $this->post('/bookings/'.$booking->id.'/cancel')->assertNotFound();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'awaiting_payment']);
    }

    public function test_settlement_and_refund_callbacks_are_idempotent_and_ledger_balances(): void
    {
        $this->freezeTime();
        $booking = app(BookingService::class)->create(User::factory()->create(), $this->payload(Trip::factory()->create()));
        $data = ['order_id' => $booking->reference, 'transaction_id' => 'gateway-123', 'gross_amount' => '1000000.00', 'transaction_status' => 'settlement'];
        $finance = app(FinanceService::class);
        $finance->applyStatus($data);
        $finance->applyStatus($data);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'paid']);
        $this->assertDatabaseCount('ledger_entries', 2);
        $finance->applyStatus([...$data, 'transaction_status' => 'refund']);
        $finance->applyStatus([...$data, 'transaction_status' => 'refund']);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'refunded']);
        $this->assertDatabaseHas('trips', ['id' => $booking->trip_id, 'reserved_seats' => 0]);
        $this->assertDatabaseCount('ledger_entries', 4);
        $this->assertSame(0, (int) LedgerEntry::sum('amount'));
    }

    public function test_expiry_command_preserves_unexpired_bookings(): void
    {
        $this->freezeTime();
        $user = User::factory()->create();
        $trip = Trip::factory()->create();
        $expired = app(BookingService::class)->create($user, $this->payload($trip));
        $expired->update(['expires_at' => now()->subMinute()]);
        $active = app(BookingService::class)->create($user, $this->payload($trip));
        $this->artisan('bookings:expire')->assertSuccessful();
        $this->assertDatabaseHas('bookings', ['id' => $expired->id, 'status' => 'expired']);
        $this->assertDatabaseHas('bookings', ['id' => $active->id, 'status' => 'awaiting_payment']);
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'reserved_seats' => 2]);
    }

    public function test_invalid_webhook_signature_cannot_mark_payment_paid(): void
    {
        config(['platform.midtrans_server_key' => 'test-server-key']);
        $payment = Payment::factory()->create();
        $this->postJson('/payments/midtrans/notification', ['order_id' => $payment->reference, 'status_code' => '200', 'gross_amount' => (string) $payment->amount, 'signature_key' => 'forged'])->assertForbidden();
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'pending']);
    }
}
