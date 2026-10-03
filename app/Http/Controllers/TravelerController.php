<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\Review;
use App\Models\RewardEntry;
use App\Models\SouvenirOrder;
use App\Models\SupportTicket;
use App\Models\TravelerProfile;
use App\Models\Trip;
use App\Services\AuditService;
use App\Services\PaymentMethodService;
use App\Services\PhoneNumberService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TravelerController extends Controller
{
    public const SECTIONS = [
        'bookings' => 'Pemesanan & Tiket', 'payments' => 'Menunggu Pembayaran', 'transactions' => 'Daftar Transaksi', 'wallet' => 'Metode Pembayaran',
        'points' => 'Points', 'vouchers' => 'Voucher', 'favorites' => 'OT & OP Favorit', 'travelers' => 'Daftar Wisatawan',
        'chat' => 'Chat', 'reviews' => 'Rating & Ulasan', 'support' => 'Pesan Bantuan', 'settings' => 'Akun Saya',
    ];

    public function index(Request $request, PaymentMethodService $methods, ?string $section = null): Response
    {
        $section ??= array_search($request->input('section'), self::SECTIONS, true) ?: 'bookings';
        abort_unless(isset(self::SECTIONS[$section]), 404);
        $user = $request->user();
        $query = match ($section) {
            'bookings' => Booking::with('trip:id,title,image_url,departure_date,destination')->where('user_id', $user->id),
            'transactions', 'payments' => Payment::with('booking:id,reference,user_id,expires_at')->whereHas('booking', fn ($q) => $q->where('user_id', $user->id)),
            'points' => RewardEntry::where('user_id', $user->id),
            'vouchers' => Promotion::where('status', 'published')->whereDate('starts_at', '<=', today())->whereDate('ends_at', '>=', today()),
            'favorites' => Favorite::with('trip:id,title,image_url,destination,price,status')->where('user_id', $user->id),
            'travelers' => TravelerProfile::where('user_id', $user->id),
            'reviews' => Review::with('trip:id,title')->where('user_id', $user->id),
            'support', 'chat' => SupportTicket::where('user_id', $user->id),
            default => null,
        };

        $transactions = null;
        if ($section === 'transactions') {
            $tripRows = DB::table('payments')->join('bookings', 'bookings.id', '=', 'payments.booking_id')
                ->where('bookings.user_id', $user->id)->where('bookings.status', 'completed')
                ->where(function ($query) {
                    $query->where('payments.status', '!=', 'pending')->orWhere('bookings.expires_at', '<=', now());
                })->select('payments.id', 'payments.created_at')->selectRaw("'trip' as kind");
            $souvenirRows = DB::table('souvenir_orders')->where('user_id', $user->id)->where('status', 'completed')
                ->where(function ($query) {
                    $query->where('status', '!=', 'awaiting_payment')->orWhere('expires_at', '<=', now());
                })->select('id', 'created_at')->selectRaw("'souvenir' as kind");
            $transactions = DB::query()->fromSub($tripRows->unionAll($souvenirRows), 'transactions')
                ->orderByDesc('created_at')->orderBy('kind')->orderByDesc('id')->paginate(3)->withQueryString();
            $rows = $transactions->getCollection();
            $payments = Payment::with('booking.trip', 'booking.vendor')->whereIn('id', $rows->where('kind', 'trip')->pluck('id'))->get()->keyBy('id');
            $orders = SouvenirOrder::with('items', 'payment', 'vendor:id,name')->whereIn('id', $rows->where('kind', 'souvenir')->pluck('id'))->get()->keyBy('id');
            $transactions->setCollection($rows->map(fn ($row) => ['kind' => $row->kind, 'record' => $row->kind === 'trip' ? $payments->get($row->id) : $orders->get($row->id)]));
        }

        return Inertia::render('Account', [
            'liveData' => true, 'sectionKey' => $section, 'sectionLabel' => self::SECTIONS[$section],
            'paymentMethods' => $methods->catalog(),
            'paymentPreferences' => $methods->preferences($user),
            'transactions' => $transactions,
            'souvenirOrders' => in_array($section, ['bookings', 'payments'], true) ? SouvenirOrder::where('user_id', $user->id)->with('items', 'payment', 'vendor:id,name')->latest('id')->paginate(10, ['*'], 'souvenir_page')->withQueryString() : null,
            'records' => $section === 'transactions' ? null : $query?->orderByDesc('id')->paginate(10)->withQueryString(),
            'pointBalance' => $section === 'points' ? RewardEntry::where('user_id', $user->id)->sum('points') : null,
            'reviewable' => $section === 'reviews' ? Booking::with('trip:id,title')->where('user_id', $user->id)->where('status', 'completed')->whereNotIn('id', Review::select('booking_id'))->latest('id')->limit(50)->get() : [],
        ]);
    }

    public function profile(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'phone' => ['nullable', 'string', 'max:30'], 'city' => ['nullable', 'string', 'max:100']]);
        if (! empty($data['phone'])) {
            $data['phone'] = PhoneNumberService::forAccount($data['phone'], $request->user()->id);
        }
        try {
            $request->user()->update($data);
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['phone' => 'Nomor HP tidak tersedia.']);
        }
        $audit->record('profile.updated', $request->user());

        return back()->with('success', 'Profil berhasil disimpan.');
    }

    public function password(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $request->validate(['current_password' => ['required', 'string', 'current_password'], 'password' => ['required', 'string', 'max:128', 'confirmed', 'different:current_password', Password::min(10)->letters()->numbers()]]);
        $required = $request->user()->must_change_password;
        $request->user()->forceFill(['password' => $data['password'], 'must_change_password' => false, 'remember_token' => Str::random(60)])->save();
        $request->session()->regenerate();
        $audit->record('user.password_changed', $request->user(), ['initial_password_replaced' => $required]);
        if ($required) {
            return to_route($request->user()->hasPermission('admin.access') ? 'admin.dashboard' : 'account')->with('success', 'Kata sandi baru tersimpan.');
        }

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    public function traveler(Request $request, ?TravelerProfile $traveler = null): RedirectResponse|JsonResponse
    {
        if ($traveler) {
            abort_unless($traveler->user_id === $request->user()->id, 404);
        }
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'birth_date' => ['nullable', 'date', 'before_or_equal:today'], 'phone' => ['nullable', 'string', 'max:30'], 'emergency_contact' => ['nullable', 'string', 'max:255']]);
        if ($traveler) {
            $traveler->update($data);
        } else {
            $traveler = TravelerProfile::create([...$data, 'user_id' => $request->user()->id]);
        }

        if ($request->expectsJson()) {
            return response()->json(['traveler' => $traveler->only(['id', 'name', 'birth_date', 'phone', 'emergency_contact'])]);
        }

        return back()->with('success', 'Data wisatawan tersimpan.');
    }

    public function removeTraveler(Request $request, TravelerProfile $traveler): RedirectResponse
    {
        abort_unless($traveler->user_id === $request->user()->id, 404);
        $traveler->delete();

        return back()->with('success', 'Wisatawan dihapus.');
    }

    public function favorite(Request $request, Trip $trip): RedirectResponse
    {
        abort_unless($trip->status === 'published' && $trip->vendor->status === 'verified', 404);
        Favorite::firstOrCreate(['user_id' => $request->user()->id, 'trip_id' => $trip->id]);

        return back()->with('success', 'Trip ditambahkan ke favorit.');
    }

    public function removeFavorite(Request $request, Favorite $favorite): RedirectResponse
    {
        abort_unless($favorite->user_id === $request->user()->id, 404);
        $favorite->delete();

        return back()->with('success', 'Favorit dihapus.');
    }

    public function review(Request $request): RedirectResponse
    {
        $data = $request->validate(['booking_id' => ['required', 'integer'], 'rating' => ['required', 'integer', 'between:1,5'], 'body' => ['required', 'string', 'min:10', 'max:5000']]);
        DB::transaction(function () use ($request, $data) {
            $booking = Booking::where('user_id', $request->user()->id)->where('status', 'completed')->lockForUpdate()->findOrFail($data['booking_id']);
            abort_if(Review::where('booking_id', $booking->id)->exists(), 422, 'Pesanan sudah diulas.');
            Review::create([...$data, 'user_id' => $request->user()->id, 'trip_id' => $booking->trip_id, 'status' => 'published']);
        });

        return back()->with('success', 'Terima kasih, ulasan berhasil dikirim.');
    }

    public function ticket(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate(['subject' => ['required', 'string', 'max:180'], 'category' => ['required', Rule::in(['booking', 'payment', 'account', 'vendor', 'other'])], 'body' => ['required', 'string', 'min:10', 'max:5000'], 'booking_id' => ['nullable', 'integer', 'prohibits:souvenir_order_id'], 'souvenir_order_id' => ['nullable', 'integer', 'prohibits:booking_id']]);
        $booking = empty($data['booking_id']) ? null : Booking::where('user_id', $request->user()->id)->findOrFail($data['booking_id']);
        $order = empty($data['souvenir_order_id']) ? null : SouvenirOrder::where('user_id', $request->user()->id)->findOrFail($data['souvenir_order_id']);
        if ($data['category'] === 'vendor' && ! $booking && ! $order) {
            throw ValidationException::withMessages(['booking_id' => 'Pilih pesanan milik kamu untuk menghubungi vendor.']);
        }
        $ticket = DB::transaction(function () use ($request, $data, $booking, $order) {
            $ticket = SupportTicket::create(['user_id' => $request->user()->id, 'booking_id' => $booking?->id, 'souvenir_order_id' => $order?->id, 'vendor_id' => $data['category'] === 'vendor' ? ($booking?->vendor_id ?? $order?->vendor_id) : null, 'subject' => $data['subject'], 'category' => $data['category']]);
            $ticket->messages()->create(['user_id' => $request->user()->id, 'body' => $data['body']]);

            return $ticket;
        });

        if ($request->expectsJson()) {
            return response()->json(['ticket' => $ticket], 201);
        }

        return to_route('support.show', $ticket);
    }

    private function authorizeTicket(Request $request, SupportTicket $ticket): void
    {
        $isVendor = $ticket->vendor_id && $request->user()->hasPermission('vendor.access') && $request->user()->vendor?->id === $ticket->vendor_id;
        abort_unless($ticket->user_id === $request->user()->id || $request->user()->hasPermission('operations.view') || $isVendor, 404);
    }

    public function conversation(Request $request, SupportTicket $ticket): Response|JsonResponse
    {
        $this->authorizeTicket($request, $ticket);

        $data = ['ticket' => $ticket, 'messages' => $ticket->messages()->with('user:id,name')->latest('id')->paginate(30)];

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Conversation', $data);
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse|JsonResponse
    {
        $this->authorizeTicket($request, $ticket);
        $isAssignedVendor = $request->user()->hasPermission('vendor.access') && $ticket->vendor_id && $request->user()->vendor?->id === $ticket->vendor_id;
        if ($ticket->user_id !== $request->user()->id && ! $isAssignedVendor) {
            abort_unless($request->user()->hasPermission('operations.manage'), 403);
        }
        abort_if($ticket->status === 'closed', 422, 'Percakapan sudah ditutup.');
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $message = $ticket->messages()->create(['user_id' => $request->user()->id, 'body' => $data['body']]);

        if ($request->expectsJson()) {
            return response()->json(['message' => $message->load('user:id,name')], 201);
        }

        return back()->with('success', 'Pesan terkirim.');
    }
}
