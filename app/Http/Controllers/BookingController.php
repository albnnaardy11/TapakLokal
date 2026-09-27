<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Refund;
use App\Models\Review;
use App\Models\Trip;
use App\Models\VirtualTour;
use App\Services\AuditService;
use App\Services\BookingService;
use App\Services\PublicContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(
        protected PublicContentService $content
    ) {}

    public function catalog(Request $request): Response
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['open-trip', 'private-trip'])],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $trips = Trip::with('vendor:id,name')->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
            ->where('departure_date', '>=', today()->toDateString())
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('title', 'like', '%'.$term.'%')->orWhere('destination', 'like', '%'.$term.'%')))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['date'] ?? null, fn ($query, $date) => $query->where('departure_date', $date))
            ->when($filters['guests'] ?? null, fn ($query, $guests) => $query->whereRaw('(capacity - reserved_seats) >= ?', [$guests]))
            ->orderBy('departure_date')->orderBy('id')->paginate(12, ['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'image_url', 'departure_date', 'end_date', 'capacity', 'reserved_seats', 'price'])->withQueryString();

        $homepageData = $this->content->homepage();

        return Inertia::render('Catalog', [
            'trips' => $trips,
            'filters' => $filters,
            'cmsDestinations' => $homepageData['cmsDestinations'] ?? [],
            'featuredTrips' => $homepageData['featuredTrips'] ?? [],
            'cmsFaqs' => $homepageData['cmsFaqs'] ?? [],
        ]);
    }

    public static array $popularPartners = [
        'explorer' => [
            'id' => 'explorer',
            'name' => 'Explorer.id',
            'logo' => '/Assets/Images/logo-vendor/explorer.webp',
            'city' => 'DKI Jakarta & Jawa Barat',
            'tagline' => 'Platform Open Trip & Private Trip Terbesar di Indonesia',
        ],
        'kilikili' => [
            'id' => 'kilikili',
            'name' => 'Kili Kili Adventure',
            'logo' => '/Assets/Images/logo-vendor/kilikili.webp',
            'city' => 'Jakarta, Jabar & Banten',
            'tagline' => 'Pelopor Open Trip: Berangkat Gak Kenal Pulang Jadi Saudara',
        ],
        'javawisata' => [
            'id' => 'javawisata',
            'name' => 'Java Wisata',
            'logo' => '/Assets/Images/logo-vendor/javawisata.webp',
            'city' => 'Bandung, Jawa Barat',
            'tagline' => 'Spesialis Tur Eksplorasi Priangan & Jawa Barat Sejak 2003',
        ],
        'tourbandung' => [
            'id' => 'tourbandung',
            'name' => 'Tour Bandung',
            'logo' => '/Assets/Images/logo-vendor/tourbandung.webp',
            'city' => 'Bandung, Jawa Barat',
            'tagline' => 'Penyedia Paket Wisata Bandung, Ciwidey & Lembang Terpercaya',
        ],
        'campatour' => [
            'id' => 'campatour',
            'name' => 'Campa Tour',
            'logo' => '/Assets/Images/logo-vendor/campatour.webp',
            'city' => 'Bogor & Sukabumi, Jawa Barat',
            'tagline' => 'Spesialis Trekking Sentul, Ekowisata & Petualangan Alam Jabar',
        ],
        'labirutour' => [
            'id' => 'labirutour',
            'name' => 'Labiru Tour',
            'logo' => '/Assets/Images/logo-vendor/labirutour.webp',
            'city' => 'Yogyakarta & Solo',
            'tagline' => 'Pemenang Penghargaan Tour Operator Terbaik Jawa Tengah & DIY',
        ],
        'rajawisata' => [
            'id' => 'rajawisata',
            'name' => 'Raja Wisata',
            'logo' => '/Assets/Images/logo-vendor/rajawisata.webp',
            'city' => 'DKI Jakarta & Jawa Barat',
            'tagline' => 'Everybody Needs Vacation: Wisata Bahari & Eksplorasi Jawa',
        ],
        'indonesiajuara' => [
            'id' => 'indonesiajuara',
            'name' => 'IndonesiaJuara',
            'logo' => '/Assets/Images/logo-vendor/indonesiajuara.webp',
            'city' => 'Malang & Banyuwangi',
            'tagline' => 'Operator Tur Petualangan Premium & Dokumentasi Sinematik',
        ],
        'funtrips' => [
            'id' => 'funtrips',
            'name' => 'Funtrips Tour',
            'logo' => '/Assets/Images/logo-vendor/funtrips.webp',
            'city' => 'Jakarta & Jawa Barat',
            'tagline' => 'Penyelenggara Open Trip Terpercaya & Outing Gathering Perusahaan',
        ],
        'brenggo' => [
            'id' => 'brenggo',
            'name' => 'BRENGGO.ID',
            'logo' => '/Assets/Images/logo-vendor/brenggo.webp',
            'city' => 'Malang, Jawa Timur',
            'tagline' => 'Spesialis Bromo, Kawah Ijen & Eksplorasi Eksotis Jawa Timur',
        ],
    ];

    public function partner(Request $request, string $partner): Response
    {
        $partnerData = self::$popularPartners[$partner] ?? null;
        abort_unless($partnerData, 404);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['open-trip', 'private-trip'])],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $trips = Trip::with('vendor:id,name')->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
            ->where('departure_date', '>=', today()->toDateString())
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('title', 'like', '%'.$term.'%')->orWhere('destination', 'like', '%'.$term.'%')))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['date'] ?? null, fn ($query, $date) => $query->where('departure_date', $date))
            ->when($filters['guests'] ?? null, fn ($query, $guests) => $query->whereRaw('(capacity - reserved_seats) >= ?', [$guests]))
            ->orderBy('departure_date')->orderBy('id')->paginate(12, ['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'image_url', 'departure_date', 'end_date', 'capacity', 'reserved_seats', 'price'])->withQueryString();

        $homepageData = $this->content->homepage();

        return Inertia::render('TripCategory', [
            'partner' => $partnerData,
            'type' => $filters['type'] ?? '',
            'trips' => $trips,
            'filters' => $filters,
            'cmsDestinations' => $homepageData['cmsDestinations'] ?? [],
            'featuredTrips' => $homepageData['featuredTrips'] ?? [],
            'cmsFaqs' => $homepageData['cmsFaqs'] ?? [],
        ]);
    }

    public function tripType(Request $request, string $type): Response
    {
        abort_unless(in_array($type, ['open-trip', 'private-trip']), 404);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $filters['type'] = $type;

        $trips = Trip::with('vendor:id,name')->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
            ->where('type', $type)
            ->where('departure_date', '>=', today()->toDateString())
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('title', 'like', '%'.$term.'%')->orWhere('destination', 'like', '%'.$term.'%')))
            ->when($filters['date'] ?? null, fn ($query, $date) => $query->where('departure_date', $date))
            ->when($filters['guests'] ?? null, fn ($query, $guests) => $query->whereRaw('(capacity - reserved_seats) >= ?', [$guests]))
            ->orderBy('departure_date')->orderBy('id')->paginate(12, ['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'image_url', 'departure_date', 'end_date', 'capacity', 'reserved_seats', 'price'])->withQueryString();

        $homepageData = $this->content->homepage();

        return Inertia::render('TripCategory', [
            'type' => $type,
            'trips' => $trips,
            'filters' => $filters,
            'cmsDestinations' => $homepageData['cmsDestinations'] ?? [],
            'featuredTrips' => $homepageData['featuredTrips'] ?? [],
            'cmsFaqs' => $homepageData['cmsFaqs'] ?? [],
        ]);
    }

    public function detail(string $tripType, string $trip): Response
    {
        $record = Trip::with('vendor:id,name')->where('type', $tripType)->where('slug', $trip)->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))->firstOrFail();

        return Inertia::render('TripDetail', [
            'tripType' => $tripType, 'trip' => $trip, 'tripData' => $record,
            'bookingKey' => (string) Str::uuid(),
            'virtualTours' => VirtualTour::visible()->where('trip_id', $record->id)->orderBy('position')->orderBy('id')->limit(12)->get()->map(fn ($tour) => $tour->presentation()),
            'reviews' => Review::with('user:id,name')->where('trip_id', $record->id)->where('status', 'published')->latest('id')->limit(10)->get(['id', 'user_id', 'rating', 'body', 'vendor_response', 'created_at']),
        ]);
    }

    public function store(BookingRequest $request, BookingService $service): RedirectResponse
    {
        return to_route('bookings.show', $service->create($request->user(), $request->validated()));
    }

    public function show(Request $request, Booking $booking): Response
    {
        abort_unless($booking->user_id === $request->user()->id, 404);

        return Inertia::render('Booking', ['booking' => $booking->load(['trip', 'vendor:id,name,phone', 'payment', 'refund']), 'gatewayReady' => (bool) config('platform.midtrans_server_key')]);
    }

    public function cancel(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 404);
        $service->transition($booking, 'cancelled');

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function refund(Request $request, Booking $booking, AuditService $audit): RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 404);
        $data = $request->validate(['reason' => ['required', 'string', 'min:10', 'max:2000']]);
        DB::transaction(function () use ($booking, $data, $request, $audit) {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($booking->status, ['paid', 'confirmed', 'ongoing', 'completed']) && $booking->payment?->status === 'paid', 422);
            abort_if($booking->payout && in_array($booking->payout->status, ['pending', 'processing', 'paid']), 422, 'Payout sedang diproses. Hubungi dukungan untuk sengketa.');
            $refund = Refund::firstOrCreate(['booking_id' => $booking->id], ['user_id' => $request->user()->id, 'amount' => $booking->total, 'reason' => $data['reason'], 'status' => 'pending']);
            if ($refund->wasRecentlyCreated) {
                $audit->record('refund.requested', $refund);
            }
        });

        return back()->with('success', 'Permintaan refund tercatat untuk ditinjau.');
    }
}
