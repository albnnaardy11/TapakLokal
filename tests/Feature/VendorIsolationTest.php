<?php

namespace Tests\Feature;

use App\Models\{Booking, SupportTicket, Trip, User, Vendor};
use App\Services\AccessService;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class VendorIsolationTest extends TestCase
{
    public function test_vendor_cannot_read_or_mutate_another_vendors_trip_or_booking(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'verified']);
        $user = $vendor->user;
        app(AccessService::class)->grant($user, 'vendor_admin');
        $trip = Trip::factory()->create();
        $booking = Booking::factory()->create();
        $this->actingAs($user)->get('/vendor/trips/'.$trip->id.'/edit')->assertNotFound();
        $this->put('/vendor/bookings/'.$booking->id, ['status' => 'confirmed'])->assertNotFound();
        $this->get('/vendor/trips')->assertInertia(fn (Assert $page) => $page->has('records.data', 0));
    }

    public function test_vendor_with_readonly_operations_permission_cannot_reply_to_unassigned_ticket(): void
    {
        $vendor = Vendor::factory()->create();
        $user = $vendor->user;
        app(AccessService::class)->grant($user, 'vendor_admin');
        $role = \App\Models\Role::create(['name' => 'support_reader', 'label' => 'Read support']);
        $role->permissions()->attach(\App\Models\Permission::where('name', 'operations.view')->firstOrFail());
        $user->roles()->attach($role);
        $ticket = SupportTicket::factory()->create();
        $this->actingAs($user)->post('/support/'.$ticket->id.'/messages', ['body' => 'Unauthorized response'])->assertForbidden();
        $this->assertDatabaseCount('support_messages', 0);
    }

    public function test_unrelated_traveler_cannot_read_support_conversation(): void
    {
        $ticket = SupportTicket::factory()->create();
        $this->actingAs(User::factory()->create())->get('/support/'.$ticket->id)->assertNotFound();
    }

    public function test_unverified_vendor_trips_are_hidden_from_search_and_detail(): void
    {
        $trip = Trip::factory()->for(Vendor::factory()->state(['status' => 'suspended']))->create();
        $this->get('/jelajah')->assertInertia(fn (Assert $page) => $page->has('trips.data', 0));
        $this->get('/trips/'.$trip->type.'/'.$trip->slug)->assertNotFound();
        $this->actingAs(User::factory()->create())->post('/bookings', ['trip_id' => $trip->id, 'participants' => 1, 'contact_name' => 'Traveler', 'contact_phone' => '08123456789', 'idempotency_key' => (string) \Illuminate\Support\Str::uuid()])->assertSessionHasErrors('trip_id');
        $this->assertDatabaseCount('bookings', 0);
    }
}
