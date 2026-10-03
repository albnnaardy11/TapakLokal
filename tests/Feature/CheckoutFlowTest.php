<?php

namespace Tests\Feature;

use App\Jobs\ReconcilePayment;
use App\Models\Booking;
use App\Models\SouvenirCartItem;
use App\Models\SouvenirOrder;
use App\Models\SouvenirProduct;
use App\Models\TravelerProfile;
use App\Models\Trip;
use App\Models\User;
use App\Services\BookingService;
use App\Services\FinanceService;
use App\Services\SouvenirCommerceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    private function booking(): Booking
    {
        $this->freezeTime();
        config(['platform.midtrans_server_key' => 'test-secret', 'platform.midtrans_production' => false]);

        return app(BookingService::class)->create(User::factory()->create(), ['trip_id' => Trip::factory()->create()->id, 'participants' => 1, 'contact_name' => 'Traveler', 'contact_phone' => '08123456789', 'idempotency_key' => (string) Str::uuid()]);
    }

    public function test_checkout_requires_login_and_isolates_order_owners(): void
    {
        $booking = $this->booking();
        $this->get(route('checkout.review.trip', $booking->trip_id))->assertRedirect('/login');
        $this->get(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]))->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]))->assertNotFound();
        $this->post(route('checkout.charge', ['type' => 'trip', 'id' => $booking->id]), ['method' => 'bca'])->assertNotFound();
        $this->actingAs($booking->user)->get(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]))->assertOk()->assertInertia(fn (Assert $page) => $page->component('CheckoutPayment')->where('order.reference', $booking->reference));
    }

    public function test_review_creates_one_reservation_and_persists_contact_and_participants(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create();
        $data = ['trip_id' => $trip->id, 'participants' => 2, 'contact_name' => 'Pemesan', 'contact_phone' => '081234567890', 'contact_email' => 'pemesan@example.test', 'traveler_details' => [['name' => 'Pemesan', 'profile_id' => null], ['name' => 'Peserta', 'profile_id' => null]], 'special_request' => 'Makanan vegetarian', 'checkout_flow' => true, 'idempotency_key' => (string) Str::uuid()];
        $this->actingAs($user)->post(route('bookings.store'), $data)->assertRedirect();
        $booking = Booking::where('user_id', $user->id)->firstOrFail();
        $this->post(route('bookings.store'), $data)->assertRedirect(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]));
        $this->assertSame(2, $trip->fresh()->reserved_seats);
        $this->assertSame($data['traveler_details'], $booking->traveler_details);
        $this->assertSame($data['contact_email'], $booking->contact_email);
        $this->post(route('bookings.store'), [...$data, 'special_request' => 'Berbeda'])->assertSessionHasErrors('idempotency_key');
        $foreign = TravelerProfile::factory()->create(['name' => 'Asing']);
        $data['traveler_details'][1]['profile_id'] = $foreign->id;
        $this->post(route('bookings.store'), $data)->assertSessionHasErrors('traveler_details.1.profile_id');
    }

    public function test_bank_instructions_use_server_total_and_are_reused_without_a_second_charge(): void
    {
        $booking = $this->booking();
        $level = DB::transactionLevel();
        Http::preventStrayRequests();
        Http::fake([
            'https://api.sandbox.midtrans.com/v2/'.$booking->reference.'/status' => Http::response(['status_code' => '404'], 404),
            'https://api.sandbox.midtrans.com/v2/charge' => function ($request) use ($booking, $level) {
                $this->assertSame($level, DB::transactionLevel());
                $this->assertSame($booking->total, $request['transaction_details']['gross_amount']);
                $this->assertSame('bca', $request['bank_transfer']['bank']);

                return Http::response(['status_code' => '201', 'order_id' => $booking->reference, 'gross_amount' => $booking->total.'.00', 'transaction_id' => 'gateway-1', 'transaction_status' => 'pending', 'payment_type' => 'bank_transfer', 'va_numbers' => [['bank' => 'bca', 'va_number' => '1234567890']]]);
            },
        ]);
        $url = route('checkout.charge', ['type' => 'trip', 'id' => $booking->id]);
        $this->actingAs($booking->user)->post($url, ['method' => 'bca', 'amount' => 1])->assertRedirect();
        $this->post($url, ['method' => 'bca'])->assertRedirect();
        Http::assertSentCount(2);
        $this->assertSame(['va_number' => '1234567890'], $booking->payment->fresh()->instructions);
        $this->assertSame('pending', $booking->payment->fresh()->status);
        $this->post($url, ['method' => 'mandiri'])->assertSessionHasErrors('payment');
        $this->post(route('bookings.checkout', $booking->id))->assertSessionHasErrors('payment');
        Http::assertSentCount(2);
    }

    public function test_retry_recovers_existing_provider_transaction_and_rejects_wrong_amount(): void
    {
        $booking = $this->booking();
        Http::preventStrayRequests();
        Http::fake(['https://api.sandbox.midtrans.com/v2/'.$booking->reference.'/status' => Http::response(['status_code' => '200', 'order_id' => $booking->reference, 'gross_amount' => '1.00', 'transaction_id' => 'gateway-2', 'transaction_status' => 'pending', 'payment_type' => 'cstore', 'store' => 'alfamart', 'payment_code' => '12345'])]);
        $this->actingAs($booking->user)->post(route('checkout.charge', ['type' => 'trip', 'id' => $booking->id]), ['method' => 'alfamart'])->assertSessionHasErrors('payment');
        $this->assertNull($booking->payment->fresh()->instructions);
        Http::assertSentCount(1);
    }

    public function test_preferences_persist_and_status_check_never_self_confirms_payment(): void
    {
        $booking = $this->booking();
        $this->actingAs($booking->user)->put(route('account.payment-methods'), ['saved' => ['bca', 'gopay'], 'primary' => 'bca'])->assertRedirect();
        $this->assertSame(['saved' => ['bca', 'gopay'], 'primary' => 'bca'], $booking->user->fresh()->payment_preferences);
        $this->put(route('account.payment-methods'), ['saved' => ['wallet'], 'primary' => 'wallet'])->assertSessionHasErrors('method');
        Queue::fake([ReconcilePayment::class]);
        $this->post(route('checkout.check', ['type' => 'trip', 'id' => $booking->id]), ['status' => 'settlement'])->assertRedirect();
        Queue::assertPushed(ReconcilePayment::class, fn ($job) => $job->reference === $booking->reference);
        $this->assertSame('pending', $booking->payment->fresh()->status);
    }

    public function test_local_status_poll_verifies_payment_without_a_queue_worker_or_webhook(): void
    {
        $booking = $this->booking();
        config(['platform.payment_queue_connection' => 'sync']);
        Http::preventStrayRequests();
        Http::fake(['https://api.sandbox.midtrans.com/v2/'.$booking->reference.'/status' => Http::response([
            'order_id' => $booking->reference, 'transaction_status' => 'settlement',
            'transaction_id' => 'sandbox-settled', 'gross_amount' => $booking->total.'.00',
        ])]);

        $this->actingAs($booking->user)
            ->from(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]))
            ->post(route('checkout.check', ['type' => 'trip', 'id' => $booking->id]), ['automatic' => true, 'status' => 'pending'])
            ->assertRedirect(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]))
            ->assertSessionMissing('success');

        $this->assertSame('paid', $booking->fresh()->status);
        $this->assertSame('paid', $booking->payment->fresh()->status);
        Http::assertSentCount(1);
    }

    public function test_checkout_cancellation_releases_unpaid_reservation_once_and_rejects_other_users(): void
    {
        $booking = $this->booking();
        $booking->payment()->update(['method' => 'bca', 'instructions' => ['va_number' => '1234567890']]);
        Http::preventStrayRequests();
        Http::fake();
        $url = route('checkout.cancel', ['type' => 'trip', 'id' => $booking->id]);

        $this->actingAs(User::factory()->create())->post($url)->assertNotFound();
        $this->actingAs($booking->user)->post($url)->assertRedirect(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]));
        $this->post($url)->assertRedirect();

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertSame('cancelled', $booking->payment->fresh()->status);
        $this->assertSame(0, $booking->trip->fresh()->reserved_seats);
        Http::assertNothingSent();
    }

    public function test_paid_checkout_cannot_be_cancelled(): void
    {
        $booking = $this->booking();
        $booking->update(['status' => 'paid']);
        $booking->payment()->update(['status' => 'paid']);

        $this->actingAs($booking->user)->post(route('checkout.cancel', ['type' => 'trip', 'id' => $booking->id]))->assertSessionHasErrors('status');

        $this->assertSame('paid', $booking->fresh()->status);
        $this->assertSame(1, $booking->trip->fresh()->reserved_seats);
    }

    public function test_late_payment_after_checkout_cancellation_requires_reconciliation_without_restoring_seats(): void
    {
        $booking = $this->booking();
        $this->actingAs($booking->user)->post(route('checkout.cancel', ['type' => 'trip', 'id' => $booking->id]))->assertRedirect();
        app(FinanceService::class)->applyStatus([
            'order_id' => $booking->reference, 'transaction_status' => 'settlement',
            'transaction_id' => 'late-cancelled', 'gross_amount' => $booking->total.'.00',
        ]);

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertSame('reconciliation_required', $booking->payment->fresh()->status);
        $this->assertSame(0, $booking->trip->fresh()->reserved_seats);
    }

    public function test_souvenir_checkout_cancellation_releases_stock_once(): void
    {
        $this->freezeTime();
        $user = User::factory()->create();
        $product = SouvenirProduct::factory()->create();
        SouvenirCartItem::create(['user_id' => $user->id, 'souvenir_product_id' => $product->id, 'variant' => 'Biji utuh', 'quantity' => 1]);
        $order = app(SouvenirCommerceService::class)->createOrder($user, ['vendor_id' => $product->vendor_id, 'idempotency_key' => (string) Str::uuid(), 'contact_name' => 'Pembeli', 'contact_phone' => '081234567890', 'contact_email' => $user->email, 'method' => 'pickup', 'pickup_date' => now()->addDays(3)->toDateString()]);
        $url = route('checkout.cancel', ['type' => 'souvenir', 'id' => $order->id]);

        $this->actingAs($user)->post($url)->assertRedirect();
        $this->post($url)->assertRedirect();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame('cancelled', $order->payment->status);
        $this->assertSame(0, $product->fresh()->reserved_stock);
        $this->assertSame(20, $product->fresh()->stock);
    }

    public function test_expired_checkout_and_booking_details_share_a_closed_payment_and_release_seats_once(): void
    {
        $booking = $this->booking();
        $this->travel(31)->minutes();
        Http::preventStrayRequests();
        Http::fake();

        $this->actingAs($booking->user)->get(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]))
            ->assertOk()->assertInertia(fn (Assert $page) => $page->where('order.status', 'expired')->where('order.payment.status', 'expired'));
        $this->get(route('bookings.show', $booking))->assertOk()->assertInertia(fn (Assert $page) => $page->where('booking.status', 'expired'));

        $this->assertSame(0, $booking->trip->fresh()->reserved_seats);
        Http::assertNothingSent();
    }

    public function test_background_status_check_returns_verified_data_without_redirecting(): void
    {
        $booking = $this->booking();
        config(['platform.payment_queue_connection' => 'sync']);
        Http::preventStrayRequests();
        Http::fake(['https://api.sandbox.midtrans.com/v2/'.$booking->reference.'/status' => Http::response([
            'order_id' => $booking->reference, 'transaction_status' => 'settlement',
            'transaction_id' => 'background-settled', 'gross_amount' => $booking->total.'.00',
        ])]);

        $this->actingAs($booking->user)->postJson(route('checkout.check', ['type' => 'trip', 'id' => $booking->id]), ['automatic' => true])
            ->assertOk()->assertJsonPath('status', 'paid')->assertJsonPath('payment.status', 'paid');
        $this->assertSame('paid', $booking->fresh()->status);
        Http::assertSentCount(1);
    }

    public function test_unavailable_gateway_returns_a_recoverable_status_error_without_changing_payment(): void
    {
        $booking = $this->booking();
        config(['platform.payment_queue_connection' => 'sync']);
        Http::preventStrayRequests();
        Http::fake(['https://api.sandbox.midtrans.com/*' => Http::failedConnection()]);

        $this->actingAs($booking->user)->postJson(route('checkout.check', ['type' => 'trip', 'id' => $booking->id]), ['automatic' => true])
            ->assertStatus(503)->assertJsonPath('message', 'Midtrans belum dapat dihubungi. Pesanan tetap tersimpan; coba periksa status lagi nanti.');
        $this->assertSame('pending', $booking->payment->fresh()->status);
    }

    public function test_rejected_sandbox_key_explains_the_failure_without_creating_instructions(): void
    {
        $booking = $this->booking();
        Http::preventStrayRequests();
        Http::fake(['https://api.sandbox.midtrans.com/*' => Http::response(['error' => 'Unauthorized'], 401)]);

        $this->actingAs($booking->user)->post(route('checkout.charge', ['type' => 'trip', 'id' => $booking->id]), ['method' => 'bca'])
            ->assertSessionHasErrors(['payment' => 'Midtrans menolak kunci sandbox. Hubungi bantuan untuk memperbaiki konfigurasi pembayaran; pesanan belum dibayar.']);
        $this->assertNull($booking->payment->fresh()->instructions);
        $this->assertSame('pending', $booking->payment->fresh()->status);
        Http::assertSentCount(1);
    }

    public function test_souvenir_review_and_account_share_the_actual_order(): void
    {
        $user = User::factory()->create();
        $product = SouvenirProduct::factory()->create();
        SouvenirCartItem::create(['user_id' => $user->id, 'souvenir_product_id' => $product->id, 'variant' => 'Biji utuh', 'quantity' => 1]);
        $this->actingAs($user)->get(route('checkout.review.souvenir', $product->vendor_id))->assertOk()->assertInertia(fn (Assert $page) => $page->component('CheckoutReview')->where('cartLines.0.product.selling_price', $product->sellingPrice()));
        $this->post(route('souvenirs.orders.create'), ['vendor_id' => $product->vendor_id, 'contact_name' => 'Pembeli', 'contact_phone' => '081234567890', 'contact_email' => $user->email, 'method' => 'pickup', 'pickup_date' => now()->addDays(3)->toDateString(), 'idempotency_key' => (string) Str::uuid(), 'checkout_flow' => true])->assertRedirect();
        $order = SouvenirOrder::where('user_id', $user->id)->firstOrFail();
        $this->get(route('checkout.payment', ['type' => 'souvenir', 'id' => $order->id]))->assertOk()->assertInertia(fn (Assert $page) => $page->component('CheckoutPayment')->where('order.reference', $order->reference));
        $this->get(route('account.section', 'bookings'))->assertOk()->assertInertia(fn (Assert $page) => $page->where('souvenirOrders.data.0.reference', $order->reference));
        $this->actingAs(User::factory()->create())->get(route('checkout.review.souvenir', $product->vendor_id))->assertNotFound();
    }

    public static function paymentMethods(): array
    {
        return [
            'Mandiri' => ['mandiri', ['payment_type' => 'echannel', 'bill_key' => '123456', 'biller_code' => '70012'], ['bill_key' => '123456', 'biller_code' => '70012']],
            'Alfamart' => ['alfamart', ['payment_type' => 'cstore', 'store' => 'alfamart', 'payment_code' => '123456'], ['payment_code' => '123456']],
            'Indomaret' => ['indomaret', ['payment_type' => 'cstore', 'store' => 'indomaret', 'payment_code' => '654321'], ['payment_code' => '654321']],
            'GoPay' => ['gopay', ['payment_type' => 'gopay', 'actions' => [['name' => 'generate-qr-code', 'url' => 'https://api.sandbox.midtrans.com/v2/gopay/qr']]], ['qr_url' => 'https://api.sandbox.midtrans.com/v2/gopay/qr']],
            'OVO via QRIS' => ['ovo', ['payment_type' => 'qris', 'actions' => [['name' => 'generate-qr-code-v2', 'url' => 'https://api.sandbox.midtrans.com/v2/qris/qr'], ['name' => 'deeplink-redirect', 'url' => 'https://malicious.example/pay'], ['name' => 'generate-qr-code', 'url' => ['invalid']]]], ['qr_url' => 'https://api.sandbox.midtrans.com/v2/qris/qr']],
        ];
    }

    #[DataProvider('paymentMethods')]
    public function test_payment_channels_recover_provider_instructions_without_recharging(string $method, array $details, array $expected): void
    {
        $booking = $this->booking();
        Http::preventStrayRequests();
        Http::fake(['https://api.sandbox.midtrans.com/v2/'.$booking->reference.'/status' => Http::response([...$details, 'status_code' => '200', 'order_id' => $booking->reference, 'gross_amount' => $booking->total.'.00', 'transaction_id' => 'gateway-recovered', 'transaction_status' => 'pending'])]);
        $this->actingAs($booking->user)->post(route('checkout.charge', ['type' => 'trip', 'id' => $booking->id]), ['method' => $method])->assertRedirect();
        $this->assertSame($expected, $booking->payment->fresh()->instructions);
        Http::assertSentCount(1);
    }

    public function test_expired_reservation_cannot_create_payment_instructions(): void
    {
        $booking = $this->booking();
        $this->travel(31)->minutes();
        Http::preventStrayRequests();
        Http::fake();
        $this->actingAs($booking->user)->post(route('checkout.charge', ['type' => 'trip', 'id' => $booking->id]), ['method' => 'bca'])->assertSessionHasErrors('payment');
        Http::assertNothingSent();
        $this->assertNull($booking->payment->fresh()->method);
    }

    public function test_souvenir_payment_instructions_belong_to_the_same_reserved_order(): void
    {
        $this->freezeTime();
        config(['platform.midtrans_server_key' => 'test-secret', 'platform.midtrans_production' => false]);
        $user = User::factory()->create();
        $product = SouvenirProduct::factory()->create();
        SouvenirCartItem::create(['user_id' => $user->id, 'souvenir_product_id' => $product->id, 'variant' => 'Biji utuh', 'quantity' => 1]);
        $order = app(SouvenirCommerceService::class)->createOrder($user, ['vendor_id' => $product->vendor_id, 'idempotency_key' => (string) Str::uuid(), 'contact_name' => 'Pembeli', 'contact_phone' => '081234567890', 'contact_email' => $user->email, 'method' => 'pickup', 'pickup_date' => now()->addDays(3)->toDateString()]);
        Http::preventStrayRequests();
        Http::fake([
            'https://api.sandbox.midtrans.com/v2/'.$order->reference.'/status' => Http::response(['status_code' => '404'], 404),
            'https://api.sandbox.midtrans.com/v2/charge' => function ($request) use ($order, $user) {
                $this->assertSame($order->total, $request['transaction_details']['gross_amount']);
                $this->assertSame($user->email, $request['customer_details']['email']);

                return Http::response(['status_code' => '201', 'order_id' => $order->reference, 'gross_amount' => $order->total.'.00', 'transaction_id' => 'souvenir-gateway', 'transaction_status' => 'pending', 'payment_type' => 'cstore', 'store' => 'indomaret', 'payment_code' => '7654321']);
            },
        ]);
        $this->actingAs($user)->post(route('checkout.charge', ['type' => 'souvenir', 'id' => $order->id]), ['method' => 'indomaret'])->assertRedirect();
        $this->assertSame(['payment_code' => '7654321'], $order->payment->fresh()->instructions);
        $this->assertSame('awaiting_payment', $order->fresh()->status);
        $this->post(route('souvenirs.orders.pay', $order->id))->assertSessionHasErrors('payment');
        Http::assertSentCount(2);
    }
}
