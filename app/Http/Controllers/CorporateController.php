<?php

namespace App\Http\Controllers;

use App\Models\CorporateCompany;
use App\Models\CorporateMembership;
use App\Models\CorporateRequest;
use App\Models\Trip;
use App\Models\User;
use App\Services\AccessService;
use App\Services\AuditService;
use App\Services\BackofficeRegistry;
use App\Services\BookingService;
use App\Services\CorporateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CorporateController extends Controller
{
    public function register(): Response
    {
        return Inertia::render('CorporateRegister');
    }

    public function storeCompany(Request $request, AccessService $access, AuditService $audit): RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:180'],
            'pic_name' => ['required', 'string', 'max:150'],
            'position' => ['required', 'in:HR,General Affairs,Procurement,Finance,Management,Other'],
            'work_email' => ['required', 'email', 'max:254'],
            'phone' => ['required', 'regex:/^\+?[0-9][0-9\s-]{7,28}$/'],
            'budget_range' => ['required', 'in:under-10m,10m-50m,50m-100m,over-100m'],
            'source' => ['required', 'in:search,social,partner,event,other'],
            'consent' => ['accepted'],
            'marketing_consent' => ['required', 'boolean'],
        ];

        if (! $user) {
            $rules['password'] = ['required', 'string', 'min:8', 'max:128', 'confirmed'];
        }

        $data = $request->validate($rules);

        $company = DB::transaction(function () use ($request, $data, $user, $access, $audit): CorporateCompany {
            if (! $user) {
                $existingUser = User::where('email', $data['work_email'])->first();
                if ($existingUser) {
                    if (! Auth::attempt(['email' => $data['work_email'], 'password' => $request->password, 'status' => 'active'])) {
                        throw ValidationException::withMessages([
                            'work_email' => 'Email ini sudah terdaftar. Masukkan kata sandi akun Anda yang sesuai.',
                        ]);
                    }
                    $user = $existingUser;
                } else {
                    $user = User::create([
                        'name' => $data['pic_name'],
                        'email' => $data['work_email'],
                        'password' => $request->password,
                        'phone' => $data['phone'],
                        'status' => 'active',
                    ]);
                    $access->grant($user, 'traveler');
                    Auth::login($user);
                }
                $request->session()->regenerate();
                $request->session()->put('auth_portal', 'corporate');
            } else {
                User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            }

            $existing = CorporateCompany::where('owner_id', $user->id)->where('name', $data['name'])->first();
            if ($existing) {
                return $existing;
            }

            $companyData = collect($data)->except(['consent', 'password', 'password_confirmation'])->all();
            $company = CorporateCompany::create([
                ...$companyData,
                'owner_id' => $user->id,
                'status' => 'pending',
                'consented_at' => now(),
            ]);

            $company->memberships()->create([
                'user_id' => $user->id,
                'role' => 'owner',
                'status' => 'active',
            ]);

            $audit->record('corporate.company.registered', $company);

            return $company;
        }, 3);

        return to_route('corporate.workspace', $company)->with('success', 'Pengajuan perusahaan tersimpan. Tim operasional akan memverifikasi data sebelum pemesanan aktif.');
    }

    public function index(Request $request): Response|RedirectResponse
    {
        $hasCorporateAccess = CorporateMembership::where('user_id', $request->user()->id)->exists()
            || $request->user()->hasPermission('admin.access');

        if (! $hasCorporateAccess) {
            return to_route('corporate.register');
        }

        return Inertia::render('CorporateCompanies', ['memberships' => CorporateMembership::where('user_id', $request->user()->id)->with('company:id,name,status')->latest('id')->simplePaginate(20)]);
    }

    public function acceptInvitation(Request $request, CorporateMembership $membership, AuditService $audit): RedirectResponse
    {
        abort_unless($membership->user_id === $request->user()->id, 404);
        if ($membership->status === 'invited') {
            $membership->update(['status' => 'active']);
            $audit->record('corporate.invitation.accepted', $membership);
        }
        abort_unless($membership->status === 'active', 403);

        return to_route('corporate.workspace', $membership->corporate_company_id);
    }

    public function workspace(Request $request, CorporateCompany $company, CorporateService $service): Response
    {
        $member = $service->membership($request->user(), $company);
        $query = $company->requests()->with(['requester:id,name', 'booking:id,user_id,reference,status,total,expires_at']);
        if ($member->role === 'requester') {
            $query->where('user_id', $request->user()->id);
        }
        $filter = $request->validate(['status' => ['nullable', 'in:submitted,quoted,approved,rejected,booked,cancelled']]);

        return Inertia::render('CorporateDashboard', ['company' => $company, 'memberRole' => $member->role, 'requests' => (clone $query)->when($filter['status'] ?? null, fn ($q, $status) => $q->where('status', $status))->latest('id')->simplePaginate(15)->withQueryString(), 'members' => $member->role === 'owner' ? $company->memberships()->with('user:id,name,email')->latest('id')->simplePaginate(20, ['*'], 'members_page') : null, 'stats' => ['waiting' => (clone $query)->whereIn('status', ['submitted', 'quoted'])->count(), 'approved' => (clone $query)->where('status', 'approved')->count(), 'booked' => (clone $query)->where('status', 'booked')->count(), 'paid' => (int) (clone $query)->whereHas('booking.payment', fn ($q) => $q->where('status', 'paid'))->sum('quote_total')], 'filter' => $filter['status'] ?? '']);
    }

    public function invite(Request $request, CorporateCompany $company, CorporateService $service, AuditService $audit): RedirectResponse
    {
        $service->membership($request->user(), $company, ['owner']);
        $data = $request->validate(['email' => ['required', 'email'], 'role' => ['required', 'in:requester,approver,finance']]);
        $user = User::where('email', $data['email'])->where('status', 'active')->first();
        if (! $user) {
            throw ValidationException::withMessages(['email' => 'Pengguna perlu membuat akun TapakLokal terlebih dahulu.']);
        }
        if ($user->id === $company->owner_id) {
            throw ValidationException::withMessages(['email' => 'Pemilik sudah menjadi anggota perusahaan.']);
        }
        DB::transaction(function () use ($company, $user, $data, $audit): void {
            CorporateCompany::whereKey($company->id)->lockForUpdate()->firstOrFail();
            $member = $company->memberships()->firstOrCreate(['user_id' => $user->id], ['role' => $data['role'], 'status' => 'invited']);
            if (! $member->wasRecentlyCreated) {
                throw ValidationException::withMessages(['email' => 'Pengguna sudah menjadi anggota atau sudah diundang.']);
            }
            $user->notifications()->create(['id' => (string) Str::uuid7(), 'type' => 'corporate.invitation', 'data' => ['title' => 'Undangan perusahaan', 'reference' => $company->name, 'url' => route('corporate.dashboard', [], false)]]);
            $audit->record('corporate.member.invited', $member);
        }, 3);

        return back()->with('success', 'Undangan tersedia di dashboard corporate akun penerima.');
    }

    public function removeMember(Request $request, CorporateCompany $company, CorporateMembership $membership, CorporateService $service, AuditService $audit): RedirectResponse
    {
        $service->membership($request->user(), $company, ['owner']);
        abort_unless($membership->corporate_company_id === $company->id, 404);
        abort_if($membership->role === 'owner', 422);
        $membership->delete();
        $audit->record('corporate.member.removed', $membership);

        return back()->with('success', 'Akses anggota dicabut.');
    }

    public function policy(Request $request, CorporateCompany $company, CorporateService $service, AuditService $audit): RedirectResponse
    {
        $service->membership($request->user(), $company, ['owner']);
        $data = $request->validate(['monthly_limit' => ['nullable', 'integer', 'min:100000', 'max:10000000000']]);
        $company->update($data);
        $audit->record('corporate.policy.updated', $company, $data);

        return back()->with('success', 'Batas anggaran tersimpan. Diperiksa saat persetujuan dan pemesanan.');
    }

    public function storeRequest(Request $request, CorporateCompany $company, CorporateService $service, AuditService $audit): RedirectResponse
    {
        $service->membership($request->user(), $company, ['owner', 'requester']);
        $data = $request->validate(['title' => ['required', 'string', 'max:180'], 'destination' => ['required', 'string', 'max:150'], 'departure_date' => ['required', 'date', 'after_or_equal:today'], 'end_date' => ['required', 'date', 'after_or_equal:departure_date'], 'participants' => ['required', 'integer', 'min:1', 'max:1000'], 'budget' => ['required', 'integer', 'min:1000', 'max:10000000000'], 'cost_center' => ['required', 'string', 'max:100'], 'needs' => ['required', 'string', 'min:10', 'max:10000'], 'idempotency_key' => ['required', 'uuid']]);
        DB::transaction(function () use ($request, $company, $data, $audit): void {
            $company = CorporateCompany::whereKey($company->id)->lockForUpdate()->firstOrFail();
            abort_unless($company->status === 'verified', 422, 'Perusahaan belum aktif.');
            $existing = $company->requests()->where('user_id', $request->user()->id)->where('idempotency_key', $data['idempotency_key'])->first();
            if ($existing) {
                foreach (collect($data)->except(['departure_date', 'end_date'])->all() as $key => $value) {
                    if ((string) $existing->$key !== (string) $value) {
                        throw ValidationException::withMessages(['idempotency_key' => 'Pengajuan berubah. Muat ulang formulir.']);
                    }
                }
                if ($existing->departure_date->toDateString() !== $data['departure_date'] || $existing->end_date->toDateString() !== $data['end_date']) {
                    throw ValidationException::withMessages(['idempotency_key' => 'Tanggal pengajuan berubah.']);
                }

                return;
            }
            $item = $company->requests()->create([...$data, 'user_id' => $request->user()->id, 'reference' => 'COR-'.Str::upper((string) Str::ulid()), 'status' => 'submitted']);
            $audit->record('corporate.request.submitted', $item);
        }, 3);

        return back()->with('success', 'Pengajuan diterima. Penawaran akan muncul di daftar perjalanan.');
    }

    public function travelers(Request $request, CorporateRequest $corporateRequest, CorporateService $service, AuditService $audit): RedirectResponse
    {
        $member = $service->membership($request->user(), $corporateRequest->company, ['owner', 'requester']);
        abort_if($member->role === 'requester' && $corporateRequest->user_id !== $request->user()->id, 404);
        $data = $request->validate(['travelers' => ['required', 'array', 'size:'.$corporateRequest->participants], 'travelers.*' => ['array:name'], 'travelers.*.name' => ['required', 'string', 'max:150']]);
        DB::transaction(function () use ($corporateRequest, $data, $audit): void {
            $item = CorporateRequest::whereKey($corporateRequest->id)->lockForUpdate()->firstOrFail();
            if ($item->booking_id) {
                throw ValidationException::withMessages(['travelers' => 'Pesanan sudah dibuat. Hubungi bantuan untuk perubahan peserta.']);
            }
            $item->update($data);
            $audit->record('corporate.travelers.updated', $item, ['count' => count($data['travelers'])]);
        }, 3);

        return back()->with('success', 'Daftar peserta tersimpan.');
    }

    public function decide(Request $request, CorporateRequest $corporateRequest, CorporateService $service): RedirectResponse
    {
        $data = $request->validate(['decision' => ['required', 'in:approved,rejected'], 'note' => ['required', 'string', 'min:5', 'max:2000']]);
        $service->decide($request->user(), $corporateRequest, $data['decision'], $data['note']);

        return back()->with('success', 'Keputusan pengajuan tersimpan.');
    }

    public function book(Request $request, CorporateRequest $corporateRequest, CorporateService $service): RedirectResponse
    {
        $id = $service->book($request->user(), $corporateRequest);

        return to_route('checkout.payment', ['type' => 'trip', 'id' => $id]);
    }

    public function cancel(Request $request, CorporateRequest $corporateRequest, CorporateService $service, BookingService $bookings, AuditService $audit): RedirectResponse
    {
        $member = $service->membership($request->user(), $corporateRequest->company, ['owner', 'requester', 'finance']);
        abort_if($member->role === 'requester' && $corporateRequest->user_id !== $request->user()->id, 404);
        DB::transaction(function () use ($corporateRequest, $bookings, $audit): void {
            CorporateCompany::whereKey($corporateRequest->corporate_company_id)->lockForUpdate()->firstOrFail();
            $item = CorporateRequest::whereKey($corporateRequest->id)->lockForUpdate()->firstOrFail();
            if ($item->status === 'cancelled') {
                return;
            }
            if ($item->booking_id) {
                if ($item->booking->status !== 'awaiting_payment') {
                    throw ValidationException::withMessages(['status' => 'Pesanan dibayar tidak dapat dibatalkan langsung. Ajukan refund melalui bantuan.']);
                }
                $bookings->transition($item->booking, 'cancelled');
            } elseif (! in_array($item->status, ['submitted', 'quoted', 'approved'], true)) {
                throw ValidationException::withMessages(['status' => 'Pengajuan sudah ditutup.']);
            }
            $item->update(['status' => 'cancelled']);
            $audit->record('corporate.request.cancelled', $item);
        }, 3);

        return back()->with('success', 'Pengajuan dibatalkan. Reservasi pesanan yang belum dibayar dilepas.');
    }

    public function admin(Request $request): Response
    {
        $request->attributes->set('admin_panel', 'operations');
        $search = $request->validate(['search' => ['nullable', 'string', 'max:100']]);

        return Inertia::render('CorporateOperations', ['navigation' => app(BackofficeRegistry::class)->navigation($request->user()), 'companies' => CorporateCompany::latest('id')->simplePaginate(15, ['*'], 'companies_page'), 'requests' => CorporateRequest::with(['company:id,name,status', 'requester:id,name', 'booking:id,reference,status'])->latest('id')->simplePaginate(20, ['*'], 'requests_page'), 'trips' => Trip::where('type', 'private-trip')->where('status', 'published')->whereDate('departure_date', '>=', today())->whereHas('vendor', fn ($q) => $q->where('status', 'verified'))->when($search['search'] ?? null, fn ($q, $value) => $q->where('title', 'like', '%'.$value.'%'))->with('vendor:id,name')->orderBy('departure_date')->simplePaginate(15, ['*'], 'trips_page')->withQueryString()]);
    }

    public function verify(Request $request, CorporateCompany $company, AuditService $audit): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:verified,rejected,suspended'], 'verification_note' => ['required', 'string', 'min:10', 'max:2000']]);
        DB::transaction(function () use ($company, $data, $audit): void {
            $company = CorporateCompany::whereKey($company->id)->lockForUpdate()->firstOrFail();
            $company->update($data);
            $audit->record('corporate.company.'.$data['status'], $company);
            User::find($company->owner_id)?->notifications()->create(['id' => (string) Str::uuid7(), 'type' => 'corporate.verification', 'data' => ['title' => 'Status perusahaan diperbarui', 'reference' => $company->name, 'url' => route('corporate.workspace', $company->id, false)]]);
        }, 3);

        return back()->with('success', 'Status perusahaan diperbarui.');
    }

    public function offer(Request $request, CorporateRequest $corporateRequest, CorporateService $service): RedirectResponse
    {
        $data = $request->validate(['trip_id' => ['required', 'integer'], 'terms' => ['required', 'string', 'min:20', 'max:10000']]);
        $service->offer($corporateRequest, $data['trip_id'], $data['terms']);

        return back()->with('success', 'Penawaran dikirim ke workspace perusahaan.');
    }

    public function report(Request $request, CorporateCompany $company, CorporateService $service): StreamedResponse
    {
        $service->membership($request->user(), $company, ['owner', 'finance', 'approver']);

        return response()->streamDownload(function () use ($company): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Referensi', 'Kegiatan', 'Cost center', 'Berangkat', 'Peserta', 'Total penawaran', 'Pengajuan', 'Pembayaran', 'Pesanan'], ',', '"', '');
            $company->requests()->with('booking.payment')->orderBy('id')->chunkById(200, function ($rows) use ($output): void {
                foreach ($rows as $row) {
                    $values = [$row->reference, $row->title, $row->cost_center, $row->departure_date->toDateString(), $row->participants, $row->quote_total, $row->status, $row->booking?->payment?->status ?? '', $row->booking?->reference ?? ''];
                    fputcsv($output, array_map(fn ($value) => preg_match('/^[=+@\-\t\r]/', (string) $value) ? "'".$value : $value, $values), ',', '"', '');
                }
            });
            fclose($output);
        }, 'corporate-'.$company->id.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store']);
    }
}
