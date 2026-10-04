<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Review;
use App\Models\RewardEntry;
use App\Models\TravelerProfile;
use App\Models\User;
use Database\Seeders\CompletedCheckoutSeeder;
use Tests\TestCase;

class CompletedCheckoutSeederTest extends TestCase
{
    public function test_it_creates_an_idempotent_completed_checkout_for_every_account_surface(): void
    {
        $this->seed(CompletedCheckoutSeeder::class);

        $traveler = User::where('email', 'traveler.checkout@tapaklokal.test')->firstOrFail();
        $booking = Booking::where('user_id', $traveler->id)->firstOrFail();
        $payment = Payment::where('booking_id', $booking->id)->firstOrFail();

        $this->assertSame('completed', $booking->status);
        $this->assertSame('paid', $payment->status);
        $this->assertSame('gopay', $payment->method);
        $this->assertDatabaseHas('payouts', [
            'booking_id' => $booking->id,
            'vendor_id' => $booking->vendor_id,
            'status' => 'eligible',
        ]);
        $this->assertSame(intdiv($booking->total, 10000), RewardEntry::where('booking_id', $booking->id)->sum('points'));
        $this->assertDatabaseHas('reviews', ['booking_id' => $booking->id, 'user_id' => $traveler->id, 'status' => 'published']);
        $this->assertTrue(Favorite::where('user_id', $traveler->id)->where('trip_id', $booking->trip_id)->exists());
        $this->assertTrue(TravelerProfile::where('user_id', $traveler->id)->exists());

        $this->seed(CompletedCheckoutSeeder::class);

        $this->assertSame(1, Booking::where('user_id', $traveler->id)->count());
        $this->assertSame(1, Payment::where('booking_id', $booking->id)->count());
        $this->assertSame(1, Payout::where('booking_id', $booking->id)->count());
        $this->assertSame(1, RewardEntry::where('booking_id', $booking->id)->count());
        $this->assertSame(1, Review::where('booking_id', $booking->id)->count());
    }
}
