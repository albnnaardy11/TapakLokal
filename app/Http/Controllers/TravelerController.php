<?php
namespace App\Http\Controllers;

use App\Models\{Booking, Favorite, Payment, Promotion, Review, RewardEntry, SupportMessage, SupportTicket, TravelerProfile, Trip};
use App\Services\AuditService;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Validation\Rule;
use Inertia\{Inertia, Response};

class TravelerController extends Controller
{
    public const SECTIONS = [
        'bookings' => 'Pemesanan & Tiket', 'transactions' => 'Daftar Transaksi', 'wallet' => 'Saldo & Pembayaran',
        'points' => 'Points', 'vouchers' => 'Voucher', 'favorites' => 'OT & OP Favorit', 'travelers' => 'Daftar Wisatawan',
        'chat' => 'Chat', 'reviews' => 'Rating & Ulasan', 'support' => 'Pesan Bantuan', 'settings' => 'Akun Saya',
    ];

    public function index(Request $request, ?string $section = null): Response
    {
        $section ??= array_search($request->input('section'), self::SECTIONS, true) ?: 'bookings';
        abort_unless(isset(self::SECTIONS[$section]), 404);
        $user = $request->user();
        $query = match ($section) {
            'bookings' => Booking::with('trip:id,title,image_url,departure_date,destination')->where('user_id', $user->id),
            'transactions', 'wallet' => Payment::with('booking:id,reference,user_id')->whereHas('booking', fn ($q) => $q->where('user_id', $user->id)),
            'points' => RewardEntry::where('user_id', $user->id),
            'vouchers' => Promotion::where('status', 'published')->whereDate('starts_at', '<=', today())->whereDate('ends_at', '>=', today()),
            'favorites' => Favorite::with('trip:id,title,image_url,destination,price,status')->where('user_id', $user->id),
            'travelers' => TravelerProfile::where('user_id', $user->id),
            'reviews' => Review::with('trip:id,title')->where('user_id', $user->id),
            'support', 'chat' => SupportTicket::where('user_id', $user->id),
            default => null,
        };
        return Inertia::render('Account', [
            'liveData' => true, 'sectionKey' => $section, 'sectionLabel' => self::SECTIONS[$section],
            'records' => $query?->orderByDesc('id')->paginate(10)->withQueryString(),
            'pointBalance' => $section === 'points' ? RewardEntry::where('user_id', $user->id)->sum('points') : null,
            'reviewable' => $section === 'reviews' ? Booking::with('trip:id,title')->where('user_id', $user->id)->where('status', 'completed')->whereNotIn('id', Review::select('booking_id'))->latest('id')->limit(50)->get() : [],
        ]);
    }

    public function profile(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'phone' => ['nullable', 'string', 'max:30'], 'city' => ['nullable', 'string', 'max:100']]);
        $request->user()->update($data);
        $audit->record('profile.updated', $request->user());
        return back()->with('success', 'Profil berhasil disimpan.');
    }

    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate(['current_password' => ['required', 'current_password'], 'password' => ['required', 'confirmed', 'different:current_password', \Illuminate\Validation\Rules\Password::min(10)->letters()->numbers()]]);
        $request->user()->update(['password' => $data['password']]);
        $request->session()->regenerate();
        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    public function traveler(Request $request, ?TravelerProfile $traveler = null): RedirectResponse
    {
        if ($traveler) { abort_unless($traveler->user_id === $request->user()->id, 404); }
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'birth_date' => ['nullable', 'date', 'before_or_equal:today'], 'phone' => ['nullable', 'string', 'max:30'], 'emergency_contact' => ['nullable', 'string', 'max:255']]);
        if ($traveler) { $traveler->update($data); } else { TravelerProfile::create([...$data, 'user_id' => $request->user()->id]); }
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

    public function ticket(Request $request): RedirectResponse
    {
        $data = $request->validate(['subject' => ['required', 'string', 'max:180'], 'category' => ['required', Rule::in(['booking', 'payment', 'account', 'vendor', 'other'])], 'body' => ['required', 'string', 'min:10', 'max:5000'], 'booking_id' => ['nullable', 'integer']]);
        $booking = empty($data['booking_id']) ? null : Booking::where('user_id', $request->user()->id)->findOrFail($data['booking_id']);
        $ticket = DB::transaction(function () use ($request, $data, $booking) {
            $ticket = SupportTicket::create(['user_id' => $request->user()->id, 'booking_id' => $booking?->id, 'vendor_id' => $data['category'] === 'vendor' ? $booking?->vendor_id : null, 'subject' => $data['subject'], 'category' => $data['category']]);
            $ticket->messages()->create(['user_id' => $request->user()->id, 'body' => $data['body']]);
            return $ticket;
        });
        return to_route('support.show', $ticket);
    }

    private function authorizeTicket(Request $request, SupportTicket $ticket): void
    {
        $isVendor = $ticket->vendor_id && $request->user()->hasPermission('vendor.access') && $request->user()->vendor?->id === $ticket->vendor_id;
        abort_unless($ticket->user_id === $request->user()->id || $request->user()->hasPermission('operations.view') || $isVendor, 404);
    }

    public function conversation(Request $request, SupportTicket $ticket): Response
    {
        $this->authorizeTicket($request, $ticket);
        return Inertia::render('Conversation', ['ticket' => $ticket, 'messages' => $ticket->messages()->with('user:id,name')->latest('id')->paginate(30)]);
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorizeTicket($request, $ticket);
        $isAssignedVendor = $request->user()->hasPermission('vendor.access') && $ticket->vendor_id && $request->user()->vendor?->id === $ticket->vendor_id;
        if ($ticket->user_id !== $request->user()->id && ! $isAssignedVendor) {
            abort_unless($request->user()->hasPermission('operations.manage'), 403);
        }
        abort_if($ticket->status === 'closed', 422, 'Percakapan sudah ditutup.');
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $ticket->messages()->create(['user_id' => $request->user()->id, 'body' => $data['body']]);
        return back()->with('success', 'Pesan terkirim.');
    }
}
