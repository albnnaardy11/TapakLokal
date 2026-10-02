<?php

namespace Tests\Feature;

use App\Jobs\ReconcileSouvenirPayment;
use App\Models\SouvenirCartItem;
use App\Models\SouvenirOrder;
use App\Models\SouvenirPayment;
use App\Models\SouvenirProduct;
use App\Models\User;
use App\Services\AccessService;
use App\Services\SouvenirCommerceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class SouvenirCommerceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['platform.markup_bps' => 1000]);
    }

    /** @return array<string, mixed> */
    private function checkoutData(SouvenirProduct $product): array
    {
        return ['vendor_id' => $product->vendor_id, 'idempotency_key' => (string) Str::uuid(), 'contact_name' => 'Pembeli', 'contact_phone' => '081234567890', 'method' => 'pickup', 'pickup_date' => now()->addDays(2)->toDateString()];
    }

    private function cart(User $user, SouvenirProduct $product, int $quantity = 2): SouvenirCartItem
    {
        return SouvenirCartItem::create(['user_id' => $user->id, 'souvenir_product_id' => $product->id, 'variant' => 'Biji utuh', 'quantity' => $quantity]);
    }

    public function test_checkout_uses_server_prices_reserves_stock_and_is_idempotent(): void
    {
        $user = User::factory()->create();
        $product = SouvenirProduct::factory()->create();
        $this->cart($user, $product);
        $data = $this->checkoutData($product);

        $this->actingAs($user)->post('/oleh-oleh/pesanan', [...$data, 'total' => 1])->assertRedirect();
        $this->post('/oleh-oleh/pesanan', $data)->assertRedirect();

        $this->assertDatabaseCount('souvenir_orders', 1);
        $this->assertDatabaseHas('souvenir_orders', ['total' => 110000, 'vendor_amount' => 100000, 'platform_fee' => 10000]);
        $this->assertDatabaseHas('souvenir_products', ['id' => $product->id, 'reserved_stock' => 2, 'stock' => 20]);
        $this->assertDatabaseCount('souvenir_cart_items', 0);
        $this->post('/oleh-oleh/pesanan', [...$data, 'contact_name' => 'Berbeda'])->assertSessionHasErrors('order');
    }

    public function test_another_buyers_order_cannot_take_reserved_stock(): void
    {
        $product = SouvenirProduct::factory()->create(['stock' => 2]);
        $first = User::factory()->create();
        $second = User::factory()->create();
        $this->cart($first, $product);
        $this->cart($second, $product);
        app(SouvenirCommerceService::class)->createOrder($first, $this->checkoutData($product));

        $this->actingAs($second)->post('/oleh-oleh/pesanan', $this->checkoutData($product))->assertSessionHasErrors('order');
        $this->assertDatabaseCount('souvenir_orders', 1);
    }

    public function test_expiration_releases_stock_once_and_late_payment_requires_reconciliation(): void
    {
        $user = User::factory()->create();
        $product = SouvenirProduct::factory()->create();
        $this->cart($user, $product);
        $service = app(SouvenirCommerceService::class);
        $order = $service->createOrder($user, $this->checkoutData($product));
        $this->travel(31)->minutes();

        $this->artisan('souvenirs:maintain')->assertSuccessful();
        $this->artisan('souvenirs:maintain')->assertSuccessful();
        $service->applyStatus(['order_id' => $order->reference, 'gross_amount' => '110000.00', 'transaction_status' => 'settlement', 'transaction_id' => 'late-transaction']);

        $this->assertDatabaseHas('souvenir_products', ['id' => $product->id, 'reserved_stock' => 0, 'stock' => 20]);
        $this->assertDatabaseHas('souvenir_payments', ['reference' => $order->reference, 'status' => 'reconciliation_required']);
        $this->assertDatabaseHas('souvenir_orders', ['id' => $order->id, 'status' => 'expired']);
    }

    public function test_settlement_and_completion_are_balanced_and_duplicate_notifications_are_safe(): void
    {
        $user = User::factory()->create();
        $product = SouvenirProduct::factory()->create();
        $this->cart($user, $product);
        $service = app(SouvenirCommerceService::class);
        $order = $service->createOrder($user, $this->checkoutData($product));
        $notification = ['order_id' => $order->reference, 'gross_amount' => '110000.00', 'transaction_status' => 'settlement', 'transaction_id' => 'settlement-transaction'];

        $service->applyStatus($notification);
        $service->applyStatus($notification);
        $service->transition($order, 'processing');
        $service->transition($order, 'ready_for_pickup');
        $this->actingAs($user)->put('/oleh-oleh/pesanan/'.$order->id, ['status' => 'completed'])->assertRedirect();

        $this->assertDatabaseHas('souvenir_products', ['id' => $product->id, 'stock' => 18, 'reserved_stock' => 0]);
        $this->assertDatabaseCount('souvenir_ledger_entries', 5);
        $this->assertSame(0, (int) DB::table('souvenir_ledger_entries')->sum('amount'));
        $this->assertDatabaseHas('souvenir_ledger_entries', ['account' => 'vendor_payable', 'amount' => -100000]);
    }

    public function test_midtrans_webhook_checks_signature_and_queues_server_verification(): void
    {
        Queue::fake();
        config(['platform.midtrans_server_key' => 'test-secret']);
        $payment = SouvenirPayment::factory()->create();
        $data = ['order_id' => $payment->reference, 'status_code' => '200', 'gross_amount' => '55000.00', 'signature_key' => 'forged'];

        $this->postJson('/payments/midtrans/notification', $data)->assertForbidden();
        Queue::assertNothingPushed();
        $data['signature_key'] = hash('sha512', $data['order_id'].'20055000.00test-secret');
        $this->postJson('/payments/midtrans/notification', $data)->assertAccepted();
        Queue::assertPushed(ReconcileSouvenirPayment::class, fn ($job) => $job->reference === $payment->reference);
    }

    public function test_checkout_creates_a_midtrans_link_without_trusting_browser_amounts(): void
    {
        config(['platform.midtrans_server_key' => 'test-secret', 'platform.midtrans_production' => false]);
        Http::preventStrayRequests();
        Http::fake(['https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response(['redirect_url' => 'https://app.sandbox.midtrans.com/snap/test'])]);
        $user = User::factory()->create();
        $product = SouvenirProduct::factory()->create();
        $this->cart($user, $product);
        $service = app(SouvenirCommerceService::class);
        $order = $service->createOrder($user, $this->checkoutData($product));

        $this->assertSame('https://app.sandbox.midtrans.com/snap/test', $service->checkout($order));
        $service->checkout($order);
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request['transaction_details']['gross_amount'] === 110000 && $request['transaction_details']['order_id'] === $order->reference);
    }

    public function test_order_and_cart_ownership_are_enforced(): void
    {
        $owner = User::factory()->create();
        $product = SouvenirProduct::factory()->create();
        $line = $this->cart($owner, $product);
        $order = SouvenirOrder::factory()->create(['user_id' => $owner->id]);
        $stranger = User::factory()->create();

        $this->actingAs($stranger)->get('/oleh-oleh/pesanan/'.$order->id)->assertNotFound();
        $this->put('/oleh-oleh/keranjang/'.$line->id, ['quantity' => 0])->assertNotFound();
        $this->post('/oleh-oleh/pesanan/'.$order->id.'/bayar')->assertNotFound();
        $this->assertModelExists($line);
    }

    public function test_pickup_only_products_cannot_be_shipped_and_invalid_dates_roll_back(): void
    {
        $user = User::factory()->create();
        $product = SouvenirProduct::factory()->create(['preparation_days' => 7]);
        $this->cart($user, $product);

        $this->actingAs($user)->post('/oleh-oleh/pesanan', $this->checkoutData($product))->assertSessionHasErrors('pickup_date');
        $this->post('/oleh-oleh/pesanan', [...$this->checkoutData($product), 'method' => 'delivery', 'address' => 'Jalan Pembeli nomor 20, Jakarta 12345', 'delivery_region' => 'DKI Jakarta', 'delivery_service' => 'JNE'])->assertSessionHasErrors('delivery_service');
        $this->assertDatabaseCount('souvenir_orders', 0);
        $this->assertDatabaseHas('souvenir_products', ['id' => $product->id, 'reserved_stock' => 0]);
    }

    public function test_vendor_cannot_edit_other_vendors_product_and_admin_moderates_before_publication(): void
    {
        $product = SouvenirProduct::factory()->create();
        $other = SouvenirProduct::factory()->create();
        app(AccessService::class)->grant($product->vendor->user, 'vendor_admin');
        $payload = $product->only(['name', 'category', 'region', 'description', 'variants', 'price', 'stock', 'weight', 'preparation_days', 'availability', 'pickup_only', 'pickup_address']);

        $this->actingAs($product->vendor->user)->put('/vendor/oleh-oleh/produk/'.$other->id, $payload)->assertNotFound();
        $this->put('/vendor/oleh-oleh/produk/'.$product->id, $payload)->assertRedirect();
        $this->get('/oleh-oleh/produk/'.$product->slug)->assertNotFound();
        $this->put('/admin/oleh-oleh/'.$product->id, ['status' => 'published'])->assertForbidden();
        $admin = User::factory()->create();
        app(AccessService::class)->grant($admin, 'operations_admin');
        $this->actingAs($admin)->put('/admin/oleh-oleh/'.$product->id, ['status' => 'published'])->assertRedirect();
        $this->assertDatabaseHas('souvenir_products', ['id' => $product->id, 'status' => 'published']);
    }
}
