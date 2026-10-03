<?php

namespace Tests\Feature;

use App\Jobs\ReconcilePaymentShare;
use App\Models\Booking;
use App\Models\PaymentShare;
use App\Models\TravelerProfile;
use App\Models\Trip;
use App\Models\User;
use App\Services\BookingService;
use App\Services\SplitPaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SplitPaymentTest extends TestCase
{
    public function test_split_redirects_to_its_booking_even_when_previous_page_is_another_order(): void
    {
        $booking = $this->booking(2);
        $previous = route('checkout.payment', ['type' => 'trip', 'id' => 999]);
        $this->actingAs($booking->user)->from($previous)->post(route('split.create', $booking), ['method' => 'gopay'])
            ->assertRedirect(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]));
        $this->get(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]))->assertOk()
            ->assertInertia(fn ($page) => $page->component('CheckoutPayment')->has('splitShares', 2)->where('splitShares.0.amount', intdiv($booking->total, 2))->where('splitShares.1.amount', intdiv($booking->total, 2)));
    }

    /** @return array<string, array{string, array<string, mixed>}> */
    public static function methods(): array
    {
        $qr = ['actions' => [['name' => 'generate-qr-code', 'url' => 'https://api.sandbox.midtrans.com/test/qr-code']]];

        return [
            'bca' => ['bca', ['payment_type' => 'bank_transfer', 'va_numbers' => [['bank' => 'bca', 'va_number' => '123456789']]]],
            'mandiri' => ['mandiri', ['payment_type' => 'echannel', 'bill_key' => '123456789', 'biller_code' => '70012']],
            'alfamart' => ['alfamart', ['payment_type' => 'cstore', 'store' => 'alfamart', 'payment_code' => '123456789']],
            'indomaret' => ['indomaret', ['payment_type' => 'cstore', 'store' => 'indomaret', 'payment_code' => '123456789']],
            'gopay' => ['gopay', ['payment_type' => 'gopay', ...$qr]],
            'ovo' => ['ovo', ['payment_type' => 'qris', ...$qr]],
        ];
    }

    #[DataProvider('methods')]
    public function test_every_enabled_method_generates_distinct_gateway_transactions(string $method, array $response): void
    {
        $booking = $this->booking(2);
        $service = app(SplitPaymentService::class);
        $service->create($booking, $method);
        $shares = PaymentShare::where('booking_id', $booking->id)->get();
        Http::fake(fn ($request) => str_ends_with($request->url(), '/charge') ? Http::response([...$this->payload($shares->firstWhere('reference', $request['transaction_details']['order_id']), 'pending'), ...$response], 201) : Http::response(['status_code' => '404'], 404));
        foreach ($shares as $share) {
            $service->charge($share);
            $this->assertNotEmpty($share->fresh()->instructions);
        }
        Http::assertSentCount(4);
        $this->assertSame('awaiting_payment', $booking->fresh()->status);
    }

    public function test_split_creation_is_rejected_in_production_without_http_or_changes(): void
    {
        $booking = $this->booking(2);
        config(['platform.midtrans_production' => true]);
        $this->actingAs($booking->user)->postJson(route('split.create', $booking), ['method' => 'bca'])->assertUnprocessable();
        $this->assertSame(0, PaymentShare::where('booking_id', $booking->id)->count());
        $this->assertNull($booking->payment->fresh()->method);
        Http::assertNothingSent();
    }

    private function booking(int $count = 3): Booking
    {
        $this->freezeTime();
        config(['platform.midtrans_server_key' => 'fake-test-key', 'platform.midtrans_production' => false]);
        Http::preventStrayRequests();

        return app(BookingService::class)->create(User::factory()->create(), ['trip_id' => Trip::factory()->create()->id, 'participants' => $count, 'traveler_details' => array_map(fn ($i) => ['name' => 'Anggota '.$i, 'profile_id' => null], range(1, $count)), 'contact_name' => 'Pemesan', 'contact_email' => 'test@example.test', 'contact_phone' => '08123456789', 'idempotency_key' => (string) Str::uuid()]);
    }

    private function payload(PaymentShare $share, string $status = 'settlement'): array
    {
        return ['order_id' => $share->reference, 'transaction_id' => 'test-'.$share->id, 'gross_amount' => (string) $share->amount, 'transaction_status' => $status, 'payment_type' => 'bank_transfer', 'va_numbers' => [['bank' => 'bca', 'va_number' => '123456'.$share->id]], 'status_code' => '201'];
    }

    public function test_split_keeps_exact_total_and_is_idempotent(): void
    {
        $booking = $this->booking();
        $booking->update(['total' => 1000001]);
        $booking->payment->update(['amount' => 1000001]);
        $this->actingAs($booking->user)->post(route('split.create', $booking), ['method' => 'bca'])->assertRedirect();
        $this->post(route('split.create', $booking), ['method' => 'bca'])->assertRedirect();
        $shares = PaymentShare::where('booking_id', $booking->id)->get();
        $this->assertCount(3, $shares);
        $this->assertSame(1000001, (int) $shares->sum('amount'));
        $this->assertCount(3, $shares->pluck('reference')->unique());
        $this->assertCount(3, $shares->pluck('token')->unique());
        $this->post(route('split.create', $booking), ['method' => 'mandiri'])->assertSessionHasErrors('payment');
        Http::assertNothingSent();
    }

    public function test_only_owner_can_create_or_generate_instructions(): void
    {
        $booking = $this->booking();
        app(SplitPaymentService::class)->create($booking, 'bca');
        $share = PaymentShare::where('booking_id', $booking->id)->firstOrFail();
        $this->actingAs(User::factory()->create())->post(route('split.create', $booking), ['method' => 'bca'])->assertNotFound();
        $this->postJson(route('split.charge', [$booking, $share]))->assertNotFound();
        Http::assertNothingSent();
    }

    public function test_partial_payment_does_not_confirm_and_duplicate_settlement_does_not_duplicate_money(): void
    {
        $booking = $this->booking(2);
        $service = app(SplitPaymentService::class);
        $service->create($booking, 'bca');
        $shares = PaymentShare::where('booking_id', $booking->id)->get();
        $service->applyStatus($this->payload($shares[0]));
        $service->applyStatus($this->payload($shares[0]));
        $this->assertSame('awaiting_payment', $booking->fresh()->status);
        $this->assertSame('pending', $booking->payment->fresh()->status);
        $this->assertSame(2, DB::table('ledger_entries')->where('booking_id', $booking->id)->count());
        $service->applyStatus($this->payload($shares[1]));
        $this->assertSame('paid', $booking->fresh()->status);
        $this->assertSame('paid', $booking->payment->fresh()->status);
        $this->assertSame($booking->total, (int) DB::table('ledger_entries')->where('booking_id', $booking->id)->where('account', 'gateway_cash')->sum('amount'));
    }

    public function test_expired_partial_booking_releases_capacity_and_requests_only_received_money(): void
    {
        $booking = $this->booking(2);
        $service = app(SplitPaymentService::class);
        $service->create($booking, 'bca');
        $shares = PaymentShare::where('booking_id', $booking->id)->get();
        $service->applyStatus($this->payload($shares[0]));
        $this->travel(31)->minutes();
        app(BookingService::class)->transition($booking, 'expired');
        $service->close($booking->fresh());
        $this->assertSame(0, $booking->trip->fresh()->reserved_seats);
        $this->assertSame($shares[0]->amount, $booking->refund()->firstOrFail()->amount);
        $this->assertSame('reconciliation_required', $booking->payment->fresh()->status);
        $service->applyStatus($this->payload($shares[1]));
        $this->assertSame('expired', $booking->fresh()->status);
        $this->assertSame($booking->total, $booking->refund()->firstOrFail()->amount);
    }

    public function test_instructions_reuse_reference_and_amount_and_no_real_http_is_allowed(): void
    {
        $booking = $this->booking(2);
        $service = app(SplitPaymentService::class);
        $service->create($booking, 'bca');
        $share = PaymentShare::where('booking_id', $booking->id)->firstOrFail();
        Http::fake(['*/status' => Http::response(['status_code' => '404'], 404), '*/charge' => Http::response($this->payload($share, 'pending'), 201)]);
        $this->actingAs($booking->user)->postJson(route('split.charge', [$booking, $share]))->assertOk();
        $this->postJson(route('split.charge', [$booking, $share]))->assertOk();
        Http::assertSentCount(2);
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/charge') && $r['transaction_details']['order_id'] === $share->reference && $r['transaction_details']['gross_amount'] === $share->amount);
        $this->assertSame('123456'.$share->id, $share->fresh()->instructions['va_number']);
    }

    public function test_public_link_does_not_expose_contacts_or_other_members(): void
    {
        $booking = $this->booking(2);
        app(SplitPaymentService::class)->create($booking, 'bca');
        $share = PaymentShare::where('booking_id', $booking->id)->firstOrFail();
        $this->get(route('split.pay', $share->token))->assertOk()->assertInertia(fn ($page) => $page->component('SplitPayment')->where('share.label', $share->label)->missing('booking')->missing('share.token')->missing('share.booking_id'));
        $this->get(route('split.pay', Str::random(64)))->assertNotFound();
    }

    public function test_mismatched_gateway_amount_cannot_confirm(): void
    {
        $booking = $this->booking(2);
        $service = app(SplitPaymentService::class);
        $service->create($booking, 'bca');
        $share = PaymentShare::where('booking_id', $booking->id)->firstOrFail();
        Http::fake(['*/status' => Http::response([...$this->payload($share), 'gross_amount' => '1'])]);
        $this->postJson(route('split.status', $share->token))->assertUnprocessable();
        $this->assertSame('pending', $share->fresh()->status);
        $this->assertSame('awaiting_payment', $booking->fresh()->status);
    }

    public function test_traveler_can_be_saved_inline_without_changing_order_and_foreign_updates_are_rejected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->postJson(route('travelers.store'), ['name' => 'Anggota Baru', 'birth_date' => '2000-01-02'])->assertOk()->assertJsonPath('traveler.name', 'Anggota Baru');
        $profile = TravelerProfile::where('user_id', $user->id)->firstOrFail();
        $this->actingAs(User::factory()->create())->patchJson(route('travelers.update', $profile), ['name' => 'Asing'])->assertNotFound();
    }

    public function test_account_reconciliation_verifies_share_instead_of_nonexistent_parent_charge(): void
    {
        $booking = $this->booking(2);
        $service = app(SplitPaymentService::class);
        $service->create($booking, 'bca');
        $shares = PaymentShare::where('booking_id', $booking->id)->get();
        Http::fake(['*/'.$shares[0]->reference.'/status' => Http::response($this->payload($shares[0])), '*/'.$shares[1]->reference.'/status' => Http::response($this->payload($shares[1]))]);
        $this->actingAs($booking->user)->postJson(route('split.owner.status', $booking))->assertOk()->assertJsonPath('payment.status', 'pending');
        $this->postJson(route('split.owner.status', $booking))->assertOk()->assertJsonPath('payment.status', 'paid');
        $this->assertSame('paid', $booking->fresh()->status);
        Http::assertSentCount(2);
    }

    public function test_signed_share_webhook_queues_verification_and_rejects_forged_signatures(): void
    {
        $booking = $this->booking(2);
        app(SplitPaymentService::class)->create($booking, 'bca');
        $share = PaymentShare::where('booking_id', $booking->id)->firstOrFail();
        Queue::fake([ReconcilePaymentShare::class]);
        $data = ['order_id' => $share->reference, 'status_code' => '200', 'gross_amount' => (string) $share->amount, 'signature_key' => 'invalid'];
        $this->postJson(route('payments.webhook'), $data)->assertForbidden();
        Queue::assertNothingPushed();
        $data['signature_key'] = hash('sha512', $data['order_id'].$data['status_code'].$data['gross_amount'].'fake-test-key');
        $this->postJson(route('payments.webhook'), $data)->assertAccepted();
        Queue::assertPushed(ReconcilePaymentShare::class, fn ($job) => $job->reference === $share->reference);
        $this->assertSame('pending', $share->fresh()->status);
        Http::assertNothingSent();
    }

    public function test_gateway_timeout_keeps_share_for_retry_and_never_confirms(): void
    {
        $booking = $this->booking(2);
        app(SplitPaymentService::class)->create($booking, 'bca');
        $share = PaymentShare::where('booking_id', $booking->id)->firstOrFail();
        Http::fake(['*' => Http::failedConnection()]);
        $this->actingAs($booking->user)->postJson(route('split.charge', [$booking, $share]))->assertServiceUnavailable();
        $this->assertSame('pending', $share->fresh()->status);
        $this->assertSame(2, PaymentShare::where('booking_id', $booking->id)->count());
        $this->assertSame('awaiting_payment', $booking->fresh()->status);
    }

    public function test_reconcile_command_bounds_work_and_does_not_call_live_gateway(): void
    {
        $booking = $this->booking(3);
        app(SplitPaymentService::class)->create($booking, 'bca');
        $this->travel(2)->minutes();
        Queue::fake([ReconcilePaymentShare::class]);
        $this->artisan('payments:reconcile-splits', ['--limit' => 1])->assertSuccessful();
        Queue::assertPushed(ReconcilePaymentShare::class, 1);
        $this->artisan('payments:reconcile-splits', ['--limit' => 0])->assertExitCode(2);
        Http::assertNothingSent();
    }
}
