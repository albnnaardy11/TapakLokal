<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Vendor;
use App\Services\AccessService;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class VendorTicketTest extends TestCase
{
    public function test_paid_ticket_can_be_verified_and_checked_in_only_once_by_own_vendor(): void
    {
        $booking = Booking::factory()->create(['status' => 'paid']);
        $booking->vendor->update(['status' => 'verified']);
        Payment::factory()->create(['booking_id' => $booking->id, 'status' => 'paid']);
        $vendor = $booking->vendor->user;
        app(AccessService::class)->grant($vendor, 'vendor_admin');
        $url = URL::temporarySignedRoute('vendor.tickets.show', now()->addDay(), ['booking' => $booking->id]);
        $this->actingAs($vendor)->get($url)->assertOk()->assertInertia(fn (AssertableInertia $page) => $page->component('Vendor/Ticket')->where('booking.id', $booking->id));
        $this->post($url)->assertRedirect();
        $checkedIn = $booking->fresh()->checked_in_at;
        $this->assertNotNull($checkedIn);
        $this->travel(2)->minutes();
        $this->post($url)->assertRedirect();
        $this->assertEquals($checkedIn, $booking->fresh()->checked_in_at);
        $other = Vendor::factory()->create(['status' => 'verified'])->user;
        app(AccessService::class)->grant($other, 'vendor_admin');
        $this->actingAs($other)->get($url)->assertNotFound();
        $this->post($url)->assertNotFound();
    }

    public function test_unsigned_expired_cancelled_and_unpaid_tickets_are_rejected(): void
    {
        $booking = Booking::factory()->create();
        $booking->vendor->update(['status' => 'verified']);
        $vendor = $booking->vendor->user;
        app(AccessService::class)->grant($vendor, 'vendor_admin');
        $this->actingAs($vendor)->get(route('vendor.tickets.show', $booking))->assertForbidden();
        $expired = URL::temporarySignedRoute('vendor.tickets.show', now()->subMinute(), ['booking' => $booking->id]);
        $this->get($expired)->assertForbidden();
        $url = URL::temporarySignedRoute('vendor.tickets.show', now()->addDay(), ['booking' => $booking->id]);
        $this->get($url)->assertUnprocessable();
        $this->post($url)->assertUnprocessable();
        $booking->update(['status' => 'cancelled']);
        Payment::factory()->create(['booking_id' => $booking->id, 'status' => 'paid']);
        $this->get($url)->assertUnprocessable();
        $this->assertNull($booking->fresh()->checked_in_at);
    }
}
