<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\CorporateCompany;
use App\Models\CorporateMembership;
use App\Models\CorporateRequest;
use App\Models\Trip;
use App\Models\User;
use App\Services\AccessService;
use App\Services\CorporateService;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CorporateWorkflowTest extends TestCase
{
    private function member(CorporateCompany $company, string $role = 'requester'): User
    {
        $user = User::factory()->create();
        CorporateMembership::factory()->create(['corporate_company_id' => $company->id, 'user_id' => $user->id, 'role' => $role]);

        return $user;
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, 'operations_admin');

        return $user;
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return ['title' => 'Gathering tim', 'destination' => 'Yogyakarta', 'departure_date' => now()->addDays(7)->toDateString(), 'end_date' => now()->addDays(8)->toDateString(), 'participants' => 5, 'budget' => 10000000, 'cost_center' => 'HR', 'needs' => 'Gathering bersama transportasi dan konsumsi.', 'idempotency_key' => (string) Str::uuid()];
    }

    public function test_registration_requires_login_and_records_consent_without_auto_verification(): void
    {
        $this->get('/corporate/register')->assertOk();
        $data = ['name' => 'PT Lokal', 'pic_name' => 'PIC Lokal', 'position' => 'HR', 'work_email' => 'pic@example.com', 'phone' => '+628123456789', 'budget_range' => '10m-50m', 'source' => 'search', 'consent' => true, 'marketing_consent' => false];
        $this->post('/corporate/register', $data)->assertRedirect();
        $this->assertDatabaseCount('corporate_companies', 0);
        $user = User::factory()->create();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($user)->post('/corporate/register', [...$data, 'consent' => false])->assertSessionHasErrors('consent');
        $this->post('/corporate/register', $data)->assertRedirect('/corporate/companies/1');
        $company = CorporateCompany::firstOrFail();
        $this->assertSame('pending', $company->status);
        $this->assertNotNull($company->consented_at);
        $this->assertFalse($company->marketing_consent);
        $this->assertDatabaseHas('corporate_memberships', ['corporate_company_id' => $company->id, 'user_id' => $user->id, 'role' => 'owner', 'status' => 'active']);
        $this->post('/corporate/register', $data)->assertRedirect('/corporate/companies/1');
        $this->assertDatabaseCount('corporate_companies', 1);
    }

    public function test_pending_company_cannot_submit_and_only_operations_admin_can_activate_it(): void
    {
        $company = CorporateCompany::factory()->create();
        $user = $this->member($company, 'owner');
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($user)->post('/corporate/companies/'.$company->id.'/requests', $this->payload())->assertUnprocessable();
        $this->put('/admin/corporate/companies/'.$company->id, ['status' => 'verified', 'verification_note' => 'Identitas perusahaan telah ditinjau.'])->assertForbidden();
        $this->withSession(['auth_portal' => 'admin'])->actingAs($this->admin())->put('/admin/corporate/companies/'.$company->id, ['status' => 'verified', 'verification_note' => 'Identitas perusahaan telah ditinjau.'])->assertRedirect();
        $this->assertSame('verified', $company->fresh()->status);
    }

    public function test_company_membership_and_invitation_are_isolated(): void
    {
        $company = CorporateCompany::factory()->create();
        $owner = $this->member($company, 'owner');
        $user = User::factory()->create();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($owner)->post('/corporate/companies/'.$company->id.'/members', ['email' => $user->email, 'role' => 'finance'])->assertRedirect();
        $membership = CorporateMembership::where('user_id', $user->id)->firstOrFail();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($user)->get('/corporate/companies/'.$company->id)->assertNotFound();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs(User::factory()->create())->post('/corporate/invitations/'.$membership->id)->assertNotFound();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($user)->post('/corporate/invitations/'.$membership->id)->assertRedirect('/corporate/companies/'.$company->id);
        $this->get('/corporate/companies/'.$company->id)->assertInertia(fn (Assert $page) => $page->component('CorporateDashboard')->where('memberRole', 'finance'));
        $this->post('/corporate/companies/'.$company->id.'/members', ['email' => $owner->email, 'role' => 'approver'])->assertForbidden();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($owner)->delete('/corporate/companies/'.$company->id.'/members/'.$membership->id)->assertRedirect();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($user)->get('/corporate/companies/'.$company->id)->assertNotFound();
    }

    public function test_submission_is_idempotent_and_requester_cannot_see_another_request(): void
    {
        $company = CorporateCompany::factory()->create(['status' => 'verified']);
        $user = $this->member($company);
        $other = $this->member($company);
        CorporateRequest::factory()->create(['corporate_company_id' => $company->id, 'user_id' => $other->id]);
        $data = $this->payload();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($user)->post('/corporate/companies/'.$company->id.'/requests', $data)->assertRedirect();
        $this->post('/corporate/companies/'.$company->id.'/requests', $data)->assertRedirect();
        $this->assertSame(1, CorporateRequest::where('user_id', $user->id)->count());
        $this->post('/corporate/companies/'.$company->id.'/requests', [...$data, 'budget' => 9000000])->assertSessionHasErrors('idempotency_key');
        $this->get('/corporate/companies/'.$company->id)->assertInertia(fn (Assert $page) => $page->has('requests.data', 1)->where('requests.data.0.user_id', $user->id));
        $this->get('/corporate/companies/'.$company->id.'/report')->assertForbidden();
    }

    public function test_quote_approval_and_payment_handoff_share_actual_booking_with_vendor(): void
    {
        $company = CorporateCompany::factory()->create(['status' => 'verified']);
        $requester = $this->member($company);
        $approver = $this->member($company, 'approver');
        $finance = $this->member($company, 'finance');
        $secondFinance = $this->member($company, 'finance');
        $item = CorporateRequest::factory()->create(['corporate_company_id' => $company->id, 'user_id' => $requester->id]);
        $trip = Trip::factory()->create(['type' => 'private-trip', 'capacity' => 10]);
        $this->withSession(['auth_portal' => 'admin'])->actingAs($this->admin())->post('/admin/corporate/requests/'.$item->id.'/offer', ['trip_id' => $trip->id, 'terms' => 'Transportasi dan konsumsi termasuk; pembatalan sesuai ketentuan.'])->assertRedirect();
        $this->assertSame(0, Booking::count());
        $this->assertSame(0, $trip->fresh()->reserved_seats);
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($approver)->post('/corporate/requests/'.$item->id.'/decision', ['decision' => 'approved', 'note' => 'Anggaran kegiatan telah disetujui.'])->assertRedirect();
        $this->assertSame(0, Booking::count());
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($finance)->post('/corporate/requests/'.$item->id.'/book')->assertRedirect('/checkout/trip/1');
        $booking = Booking::firstOrFail();
        $this->assertSame($trip->selling_price * 5, $booking->total);
        $this->assertSame(5, $trip->fresh()->reserved_seats);
        $this->assertSame('awaiting_payment', $booking->status);
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($secondFinance)->post('/corporate/requests/'.$item->id.'/book')->assertRedirect('/checkout/trip/1');
        $this->assertDatabaseCount('bookings', 1);
        $this->get('/checkout/trip/'.$booking->id)->assertOk();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($requester)->get('/checkout/trip/'.$booking->id)->assertForbidden();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs(User::factory()->create())->get('/checkout/trip/'.$booking->id)->assertNotFound();
        app(AccessService::class)->grant($trip->vendor->user, 'vendor_admin');
        $this->withSession(['auth_portal' => 'vendor'])->actingAs($trip->vendor->user)->get('/vendor/bookings')->assertInertia(fn (Assert $page) => $page->has('records.data', 1)->where('records.data.0.id', $booking->id));
    }

    public function test_self_approval_and_cross_company_decisions_are_rejected(): void
    {
        $company = CorporateCompany::factory()->create(['status' => 'verified']);
        $owner = $this->member($company, 'owner');
        $item = CorporateRequest::factory()->create(['corporate_company_id' => $company->id, 'user_id' => $owner->id]);
        app(CorporateService::class)->offer($item, Trip::factory()->create(['type' => 'private-trip'])->id, 'Fasilitas dan kebijakan pembatalan tercantum.');
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($owner)->post('/corporate/requests/'.$item->id.'/decision', ['decision' => 'approved', 'note' => 'Setujui anggaran.'])->assertSessionHasErrors('decision');
        $this->withSession(['auth_portal' => 'corporate'])->actingAs(User::factory()->create())->post('/corporate/requests/'.$item->id.'/decision', ['decision' => 'approved', 'note' => 'Setujui anggaran.'])->assertNotFound();
        $this->assertSame('quoted', $item->fresh()->status);
    }

    public function test_monthly_budget_and_changed_trip_price_block_checkout(): void
    {
        $company = CorporateCompany::factory()->create(['status' => 'verified', 'monthly_limit' => 2000000]);
        $requester = $this->member($company);
        $approver = $this->member($company, 'approver');
        $finance = $this->member($company, 'finance');
        $item = CorporateRequest::factory()->create(['corporate_company_id' => $company->id, 'user_id' => $requester->id]);
        $trip = Trip::factory()->create(['type' => 'private-trip']);
        app(CorporateService::class)->offer($item, $trip->id, 'Fasilitas dan kebijakan pembatalan tercantum.');
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($approver)->post('/corporate/requests/'.$item->id.'/decision', ['decision' => 'approved', 'note' => 'Setujui anggaran.'])->assertSessionHasErrors('budget');
        $company->update(['monthly_limit' => 10000000]);
        $this->post('/corporate/requests/'.$item->id.'/decision', ['decision' => 'approved', 'note' => 'Setujui anggaran.'])->assertRedirect();
        $trip->update(['price' => 600000]);
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($finance)->post('/corporate/requests/'.$item->id.'/book')->assertSessionHasErrors('status');
        $this->assertDatabaseCount('bookings', 0);
        $this->assertSame(0, $trip->fresh()->reserved_seats);
    }

    public function test_quote_requires_matching_private_trip_and_unexpired_terms(): void
    {
        $company = CorporateCompany::factory()->create(['status' => 'verified']);
        $user = $this->member($company);
        $approver = $this->member($company, 'approver');
        $item = CorporateRequest::factory()->create(['corporate_company_id' => $company->id, 'user_id' => $user->id]);
        $admin = $this->admin();
        $this->withSession(['auth_portal' => 'admin'])->actingAs($admin)->post('/admin/corporate/requests/'.$item->id.'/offer', ['trip_id' => Trip::factory()->create()->id, 'terms' => 'Ketentuan pembatalan dan fasilitas lengkap.'])->assertSessionHasErrors('trip_id');
        $trip = Trip::factory()->create(['type' => 'private-trip']);
        $this->post('/admin/corporate/requests/'.$item->id.'/offer', ['trip_id' => $trip->id, 'terms' => 'Ketentuan pembatalan dan fasilitas lengkap.'])->assertRedirect();
        $item->update(['quote_expires_at' => now()->subMinute()]);
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($approver)->post('/corporate/requests/'.$item->id.'/decision', ['decision' => 'approved', 'note' => 'Setujui anggaran.'])->assertSessionHasErrors('decision');
    }

    public function test_report_is_company_scoped_and_neutralizes_spreadsheet_formulas(): void
    {
        $company = CorporateCompany::factory()->create(['status' => 'verified']);
        $finance = $this->member($company, 'finance');
        CorporateRequest::factory()->create(['corporate_company_id' => $company->id, 'title' => '=1+1']);
        CorporateRequest::factory()->create(['title' => 'Other company private data']);
        $response = $this->withSession(['auth_portal' => 'corporate'])->actingAs($finance)->get('/corporate/companies/'.$company->id.'/report');
        $response->assertOk()->assertDownload('corporate-'.$company->id.'.csv');
        $this->assertStringContainsString("'=1+1", $response->streamedContent());
        $this->assertStringNotContainsString('Other company private data', $response->streamedContent());
    }

    public function test_participant_roster_is_required_and_forwarded_to_vendor_booking(): void
    {
        $company = CorporateCompany::factory()->create(['status' => 'verified']);
        $requester = $this->member($company);
        $approver = $this->member($company, 'approver');
        $finance = $this->member($company, 'finance');
        $item = CorporateRequest::factory()->create(['corporate_company_id' => $company->id, 'user_id' => $requester->id, 'travelers' => null]);
        $trip = Trip::factory()->create(['type' => 'private-trip']);
        app(CorporateService::class)->offer($item, $trip->id, 'Fasilitas dan kebijakan pembatalan tercantum.');
        app(CorporateService::class)->decide($approver, $item, 'approved', 'Disetujui perusahaan.');
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($finance)->post('/corporate/requests/'.$item->id.'/book')->assertSessionHasErrors('travelers');
        $this->assertDatabaseCount('bookings', 0);
        $roster = array_map(fn ($number) => ['name' => 'Traveler '.$number], range(1, 5));
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($requester)->patch('/corporate/requests/'.$item->id.'/travelers', ['travelers' => array_slice($roster, 0, 4)])->assertSessionHasErrors('travelers');
        $this->patch('/corporate/requests/'.$item->id.'/travelers', ['travelers' => $roster])->assertRedirect();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($finance)->post('/corporate/requests/'.$item->id.'/book')->assertRedirect();
        $this->assertSame($roster, Booking::firstOrFail()->traveler_details);
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($requester)->patch('/corporate/requests/'.$item->id.'/travelers', ['travelers' => $roster])->assertSessionHasErrors('travelers');
    }

    public function test_cancel_releases_inventory_and_revoked_finance_cannot_resume_payment(): void
    {
        $company = CorporateCompany::factory()->create(['status' => 'verified']);
        $requester = $this->member($company);
        $approver = $this->member($company, 'approver');
        $finance = $this->member($company, 'finance');
        $owner = $this->member($company, 'owner');
        $item = CorporateRequest::factory()->create(['corporate_company_id' => $company->id, 'user_id' => $requester->id]);
        $trip = Trip::factory()->create(['type' => 'private-trip']);
        $service = app(CorporateService::class);
        $service->offer($item, $trip->id, 'Fasilitas dan kebijakan pembatalan tercantum.');
        $service->decide($approver, $item, 'approved', 'Disetujui perusahaan.');
        $id = $service->book($finance, $item);
        $membership = CorporateMembership::where('user_id', $finance->id)->firstOrFail();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($owner)->delete('/corporate/companies/'.$company->id.'/members/'.$membership->id)->assertRedirect();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($finance)->get('/checkout/trip/'.$id)->assertNotFound();
        $this->get('/bookings/'.$id)->assertNotFound();
        $this->post('/bookings/'.$id.'/checkout')->assertNotFound();
        $this->post('/bookings/'.$id.'/cancel')->assertNotFound();
        $this->post('/bookings/'.$id.'/refund', ['reason' => 'Permintaan tidak sah setelah akses dicabut.'])->assertNotFound();
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($owner)->post('/bookings/'.$id.'/checkout')->assertRedirect(route('checkout.payment', ['type' => 'trip', 'id' => $id]));
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($owner)->post('/corporate/requests/'.$item->id.'/cancel')->assertRedirect();
        $this->post('/corporate/requests/'.$item->id.'/cancel')->assertRedirect();
        $this->assertSame(0, $trip->fresh()->reserved_seats);
        $this->assertSame('cancelled', Booking::findOrFail($id)->status);
        $this->assertSame('cancelled', $item->fresh()->status);
    }

    public function test_revised_offer_requires_new_approval_and_preserves_history(): void
    {
        $company = CorporateCompany::factory()->create(['status' => 'verified']);
        $requester = $this->member($company);
        $approver = $this->member($company, 'approver');
        $finance = $this->member($company, 'finance');
        $item = CorporateRequest::factory()->create(['corporate_company_id' => $company->id, 'user_id' => $requester->id]);
        $trip = Trip::factory()->create(['type' => 'private-trip']);
        $service = app(CorporateService::class);
        $service->offer($item, $trip->id, 'Fasilitas dan kebijakan pembatalan tercantum.');
        $service->decide($approver, $item, 'approved', 'Disetujui perusahaan.');
        $service->offer($item, $trip->id, 'Fasilitas direvisi, kebijakan pembatalan tercantum.');
        $item->refresh();
        $this->assertCount(2, $item->quote_history);
        $this->assertNull($item->approved_by);
        $this->assertSame('quoted', $item->status);
        $this->withSession(['auth_portal' => 'corporate'])->actingAs($finance)->post('/corporate/requests/'.$item->id.'/book')->assertSessionHasErrors('status');
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $approver->id, 'type' => 'corporate.status']);
    }
}
