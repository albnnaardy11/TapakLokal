<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\SouvenirOrder;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccountTransactionHistoryTest extends TestCase
{
    public static function paymentStatuses(): array
    {
        return ['paid' => ['paid', 'paid'], 'failed' => ['failed', 'cancelled'], 'expired' => ['expired', 'expired'], 'refunded' => ['refunded', 'cancelled']];
    }

    #[DataProvider('paymentStatuses')]
    public function test_trip_payment_appears_before_booking_is_completed(string $paymentStatus, string $bookingStatus): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id, 'status' => $bookingStatus]);
        $payment = Payment::factory()->create(['booking_id' => $booking->id, 'status' => $paymentStatus]);

        $this->actingAs($user)->get('/account/transactions')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Account')->where('transactions.total', 1)
            ->where('transactions.data.0.kind', 'trip')->where('transactions.data.0.record.id', $payment->id));
    }

    public function test_paid_souvenir_order_appears_before_fulfillment(): void
    {
        $user = User::factory()->create();
        $order = SouvenirOrder::factory()->create(['user_id' => $user->id, 'status' => 'paid']);

        $this->actingAs($user)->get('/account/transactions')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('transactions.total', 1)->where('transactions.data.0.kind', 'souvenir')
            ->where('transactions.data.0.record.id', $order->id));
    }

    public function test_history_excludes_active_pending_payments_and_other_users(): void
    {
        $this->freezeTime();
        $user = User::factory()->create();
        Payment::factory()->create(['booking_id' => Booking::factory()->create(['user_id' => $user->id])->id]);
        SouvenirOrder::factory()->create(['user_id' => $user->id]);
        Payment::factory()->create(['status' => 'paid']);
        SouvenirOrder::factory()->create(['status' => 'paid']);

        $this->actingAs($user)->get('/account/transactions')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('transactions.total', 0)->has('transactions.data', 0));
    }

    public function test_expired_pending_payments_appear_in_history(): void
    {
        $this->freezeTime();
        $user = User::factory()->create();
        Payment::factory()->create(['booking_id' => Booking::factory()->create(['user_id' => $user->id, 'expires_at' => now()->subMinute()])->id]);
        SouvenirOrder::factory()->create(['user_id' => $user->id, 'expires_at' => now()->subMinute()]);

        $this->actingAs($user)->get('/account/transactions')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('transactions.total', 2)->has('transactions.data', 2));
    }
}
