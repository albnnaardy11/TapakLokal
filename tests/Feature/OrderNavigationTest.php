<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\SouvenirOrder;
use App\Models\SupportTicket;
use App\Models\Trip;
use App\Models\User;
use App\Services\AccessService;
use App\Services\BookingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class OrderNavigationTest extends TestCase
{
    public function test_guest_cannot_create_order_and_login_returns_to_trip(): void
    {
        $trip = Trip::factory()->create();
        $this->post(route('bookings.store'), ['trip_id' => $trip->id])->assertRedirect(route('login'));
        $this->assertDatabaseCount('bookings', 0);
        $this->get(route('login', ['trip' => $trip->id]))->assertRedirect(route('trips.show', [$trip->type, $trip->slug, 'auth' => 'login']));
        $this->assertEquals(route('trips.show', [$trip->type, $trip->slug, 'book' => 1]), session('url.intended'));
        $user = User::factory()->create(['password' => 'Password123!']);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'Password123!'])->assertRedirect(route('trips.show', [$trip->type, $trip->slug, 'book' => 1]));
    }

    public function test_protected_cart_login_does_not_redirect_into_auth_loop(): void
    {
        $this->get(route('souvenirs.cart'))->assertRedirect(route('login'));
        $this->get(route('login'))->assertRedirect('/?auth=login');
        $user = User::factory()->create(['password' => 'Password123!']);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'Password123!'])->assertRedirect(route('souvenirs.cart'));
    }

    public function test_login_return_target_cannot_be_an_external_site(): void
    {
        $this->get(route('login', ['return_to' => '//evil.example']))->assertSessionHasErrors('return_to');
        $this->get(route('login', ['return_to' => '/oleh-oleh']))->assertRedirect('/oleh-oleh?auth=login');
        $this->assertSame('/oleh-oleh', session('url.intended'));
    }

    public function test_real_order_notifies_buyer_and_vendor_once_for_each_status(): void
    {
        $trip = Trip::factory()->create();
        $buyer = User::factory()->create();
        $payload = ['trip_id' => $trip->id, 'participants' => 1, 'contact_name' => $buyer->name, 'contact_phone' => '08123456789', 'idempotency_key' => (string) Str::uuid()];
        $service = app(BookingService::class);
        $booking = $service->create($buyer, $payload);
        $service->create($buyer, $payload);
        $this->assertSame(1, $buyer->unreadNotifications()->count());
        $this->assertSame(1, $trip->vendor->user->unreadNotifications()->count());
        $service->transition($booking, 'cancelled');
        $service->transition($booking, 'cancelled');
        $this->assertSame(2, $buyer->unreadNotifications()->count());
        $this->assertTrue($buyer->notifications()->get()->pluck('data.status')->contains('cancelled'));
    }

    public function test_notifications_are_private_and_read_state_is_persisted(): void
    {
        $order = SouvenirOrder::factory()->create();
        $buyer = User::findOrFail($order->user_id);
        $notification = $buyer->notifications()->firstOrFail();
        $this->actingAs(User::factory()->create())->post(route('notifications.read', $notification->id))->assertNotFound();
        $this->actingAs($buyer)->get(route('notifications.index'))->assertInertia(fn (AssertableInertia $page) => $page->component('Notifications')->has('notifications.data', 1)->where('navigation.unreadCount', 1));
        $this->post(route('notifications.read', $notification->id))->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_rolled_back_order_does_not_leave_notification(): void
    {
        $buyer = User::factory()->create();
        DB::beginTransaction();
        Booking::factory()->create(['user_id' => $buyer->id]);
        DB::rollBack();
        $this->assertSame(0, $buyer->notifications()->count());
    }

    public function test_departed_trip_is_not_available_in_public_booking_form(): void
    {
        $trip = Trip::factory()->create(['departure_date' => today()->subDay(), 'end_date' => today()]);
        $this->get(route('trips.show', [$trip->type, $trip->slug]))->assertInertia(fn (AssertableInertia $page) => $page->where('canBook', false));
    }

    public function test_souvenir_chat_routes_to_own_vendor_and_notifies_both_participants(): void
    {
        $order = SouvenirOrder::factory()->create();
        $buyer = User::findOrFail($order->user_id);
        $vendor = $order->vendor->user;
        app(AccessService::class)->grant($vendor, 'vendor_admin');
        $this->actingAs($buyer)->post(route('support.store'), ['subject' => 'Tanya pesanan', 'body' => 'Kapan pesanan saya siap diambil?', 'category' => 'vendor', 'souvenir_order_id' => $order->id, 'vendor_id' => 999])->assertRedirect();
        $ticket = SupportTicket::firstOrFail();
        $this->assertSame($order->vendor_id, $ticket->vendor_id);
        $this->assertSame($order->id, $ticket->souvenir_order_id);
        $this->assertSame(1, $vendor->notifications()->where('type', 'support.message')->count());
        $this->actingAs($vendor)->get(route('support.show', $ticket))->assertOk();
        $this->post(route('support.reply', $ticket), ['body' => 'Pesanan akan siap besok pagi.'])->assertRedirect();
        $this->assertSame(1, $buyer->notifications()->where('type', 'support.message')->count());
        $this->assertDatabaseCount('support_messages', 2);
        $this->actingAs(User::factory()->create())->get(route('support.show', $ticket))->assertNotFound();
    }

    public function test_chat_cannot_target_someone_elses_order_or_unspecified_vendor(): void
    {
        $order = SouvenirOrder::factory()->create();
        $this->actingAs(User::factory()->create())->post(route('support.store'), ['subject' => 'Tanya pesanan', 'body' => 'Kapan pesanan siap diambil?', 'category' => 'vendor', 'souvenir_order_id' => $order->id])->assertNotFound();
        $this->post(route('support.store'), ['subject' => 'Tanya pesanan', 'body' => 'Kapan pesanan siap diambil?', 'category' => 'vendor'])->assertSessionHasErrors('booking_id');
        $this->assertDatabaseCount('support_tickets', 0);
        $this->assertDatabaseCount('support_messages', 0);
    }

    public function test_marking_all_notifications_read_only_changes_current_account(): void
    {
        $first = Booking::factory()->create();
        $second = Booking::factory()->create();
        $buyer = User::findOrFail($first->user_id);
        $other = User::findOrFail($second->user_id);
        $this->actingAs($buyer)->post(route('notifications.read-all'))->assertRedirect();
        $this->assertSame(0, $buyer->unreadNotifications()->count());
        $this->assertSame(1, $other->unreadNotifications()->count());
    }
}
