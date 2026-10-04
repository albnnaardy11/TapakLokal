<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\ContentPage;
use App\Models\Refund;
use App\Models\Review;
use App\Models\SouvenirProduct;
use App\Models\Trip;
use App\Models\Vendor;
use App\Models\VirtualTour;
use App\Services\AuditService;
use App\Services\BookingService;
use App\Services\CorporateService;
use App\Services\PublicContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(
        protected PublicContentService $content
    ) {}

    /**
     * Normalize Indonesian travel abbreviations and extract search tokens.
     *
     * @return array<int, string>
     */
    protected function extractSearchTokens(string $rawQuery): array
    {
        $raw = trim($rawQuery);
        if ($raw === '') {
            return [];
        }

        $lower = Str::lower($raw);
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $lower) ?? $lower;
        $clean = trim(preg_replace('/\s+/', ' ', $clean) ?? $clean);

        $replacements = [
            '/\b(gn|mt|g)\b/i' => 'gunung',
            '/\b(mount)\b/i' => 'gunung',
            '/\b(plu|p)\b/i' => 'pulau',
            '/\b(kep|k)\b/i' => 'kepulauan',
            '/\b(jogja|yogya|yk)\b/i' => 'yogyakarta',
            '/\b(bj)\b/i' => 'labuan bajo',
            '/\b(bwi)\b/i' => 'banyuwangi',
            '/\b(jkt|dki)\b/i' => 'jakarta',
            '/\b(jabar)\b/i' => 'jawa barat',
            '/\b(jatim)\b/i' => 'jawa timur',
            '/\b(jateng)\b/i' => 'jawa tengah',
            '/\b(crg)\b/i' => 'curug',
        ];

        $expanded = preg_replace(array_keys($replacements), array_values($replacements), $clean) ?? $clean;
        $expanded = trim(preg_replace('/\s+/', ' ', $expanded) ?? $expanded);

        $rawWords = array_filter(explode(' ', $clean), fn ($w) => mb_strlen($w) >= 2);
        $expandedWords = array_filter(explode(' ', $expanded), fn ($w) => mb_strlen($w) >= 2);

        $all = array_unique(array_merge([$raw, $clean, $expanded], $rawWords, $expandedWords));

        return array_values(array_filter($all, fn ($t) => trim($t) !== ''));
    }

    /**
     * Apply intelligent travel search conditions on Eloquent queries.
     */
    protected function applySmartTripSearch($query, string $term)
    {
        $tokens = $this->extractSearchTokens($term);
        if (empty($tokens)) {
            return $query;
        }

        return $query->where(function ($search) use ($tokens, $term) {
            $search->whereRaw('LOWER(title) LIKE ?', ['%'.Str::lower($term).'%'])
                ->orWhereRaw('LOWER(destination) LIKE ?', ['%'.Str::lower($term).'%'])
                ->orWhereRaw('LOWER(slug) LIKE ?', ['%'.Str::slug($term).'%']);

            foreach ($tokens as $token) {
                $lowerToken = Str::lower($token);
                $search->orWhereRaw('LOWER(title) LIKE ?', ['%'.$lowerToken.'%'])
                    ->orWhereRaw('LOWER(destination) LIKE ?', ['%'.$lowerToken.'%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%'.$lowerToken.'%'])
                    ->orWhereRaw('LOWER(meeting_point) LIKE ?', ['%'.$lowerToken.'%']);
            }
        });
    }

    public function suggestions(Request $request): JsonResponse
    {
        $rawQuery = trim((string) $request->input('q', ''));
        $tokens = $this->extractSearchTokens($rawQuery);

        $trips = Trip::with('vendor:id,name')
            ->where('status', 'published')
            ->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
            ->where('departure_date', '>=', today()->toDateString())
            ->when(! empty($tokens), fn ($query) => $this->applySmartTripSearch($query, $rawQuery))
            ->latest('id')
            ->limit(6)
            ->get()
            ->map(fn (Trip $trip) => [
                'id' => $trip->id,
                'title' => $trip->title,
                'slug' => $trip->slug,
                'type' => $trip->type,
                'type_label' => $trip->type === 'open-trip' ? 'Open Trip' : 'Private Trip',
                'destination' => $trip->destination,
                'price' => $trip->selling_price,
                'formatted_price' => 'Rp '.number_format($trip->selling_price, 0, ',', '.'),
                'image_url' => $trip->image_url,
                'vendor_name' => $trip->vendor?->name,
                'departure_date' => $trip->departure_date ? $trip->departure_date->format('d M Y') : null,
                'url' => route('trips.show', ['tripType' => $trip->type, 'trip' => $trip->slug]),
            ]);

        $destinations = ContentPage::whereIn('type', ['destination', 'hidden-gem'])
            ->where('status', 'published')
            ->when(! empty($tokens), function ($query) use ($tokens, $rawQuery) {
                $query->where(function ($q) use ($tokens, $rawQuery) {
                    $q->whereRaw('LOWER(title) LIKE ?', ['%'.Str::lower($rawQuery).'%'])
                        ->orWhereRaw('LOWER(excerpt) LIKE ?', ['%'.Str::lower($rawQuery).'%']);
                    foreach ($tokens as $token) {
                        $lowerToken = Str::lower($token);
                        $q->orWhereRaw('LOWER(title) LIKE ?', ['%'.$lowerToken.'%'])
                            ->orWhereRaw('LOWER(excerpt) LIKE ?', ['%'.$lowerToken.'%']);
                    }
                });
            })
            ->limit(4)
            ->get()
            ->map(fn (ContentPage $page) => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'image_url' => $page->image_url,
                'url' => route('catalog', ['q' => $page->title]),
            ]);

        $souvenirs = SouvenirProduct::where('status', 'published')
            ->when(! empty($tokens), function ($query) use ($tokens, $rawQuery) {
                $query->where(function ($q) use ($tokens, $rawQuery) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%'.Str::lower($rawQuery).'%'])
                        ->orWhereRaw('LOWER(region) LIKE ?', ['%'.Str::lower($rawQuery).'%']);
                    foreach ($tokens as $token) {
                        $lowerToken = Str::lower($token);
                        $q->orWhereRaw('LOWER(name) LIKE ?', ['%'.$lowerToken.'%'])
                            ->orWhereRaw('LOWER(region) LIKE ?', ['%'.$lowerToken.'%'])
                            ->orWhereRaw('LOWER(description) LIKE ?', ['%'.$lowerToken.'%']);
                    }
                });
            })
            ->limit(4)
            ->get()
            ->map(fn (SouvenirProduct $prod) => [
                'id' => $prod->id,
                'name' => $prod->name,
                'city' => $prod->region,
                'price' => $prod->price,
                'formatted_price' => 'Rp '.number_format($prod->price, 0, ',', '.'),
                'image_url' => $prod->image_url,
                'url' => route('souvenirs.show', ['product' => $prod->slug ?: $prod->id]),
            ]);

        $popularDestinations = [
            ['name' => 'Gunung Bromo', 'region' => 'Malang, Jawa Timur', 'type' => 'open-trip', 'url' => route('trips.category', ['type' => 'open-trip', 'q' => 'Bromo'])],
            ['name' => 'Labuan Bajo', 'region' => 'Taman Nasional Komodo, NTT', 'type' => 'open-trip', 'url' => route('trips.category', ['type' => 'open-trip', 'q' => 'Labuan Bajo'])],
            ['name' => 'Yogyakarta', 'region' => 'DIY & Candi Prambanan', 'type' => 'open-trip', 'url' => route('catalog', ['q' => 'Yogyakarta'])],
            ['name' => 'Bali', 'region' => 'Nusa Penida & Ubud', 'type' => 'private-trip', 'url' => route('trips.category', ['type' => 'private-trip', 'q' => 'Bali'])],
            ['name' => 'Gunung Gede', 'region' => 'Taman Nasional Gede Pangrango', 'type' => 'open-trip', 'url' => route('trips.category', ['type' => 'open-trip', 'q' => 'Gede'])],
            ['name' => 'Gunung Salak', 'region' => 'Curug & Jalur Rimba Salak', 'type' => 'open-trip', 'url' => route('trips.category', ['type' => 'open-trip', 'q' => 'Salak'])],
            ['name' => 'Kepulauan Seribu', 'region' => 'Pulau Pramuka & Pari', 'type' => 'open-trip', 'url' => route('trips.category', ['type' => 'open-trip', 'q' => 'Kepulauan Seribu'])],
            ['name' => 'Lombok', 'region' => 'Gili Trawangan & Rinjani', 'type' => 'open-trip', 'url' => route('trips.category', ['type' => 'open-trip', 'q' => 'Lombok'])],
        ];

        $quickCategories = [
            [
                'title' => 'Cari di Open Trip',
                'type' => 'open-trip',
                'badge' => 'Paling Populer',
                'description' => 'Gabung trip bersama traveler lain, hemat & seru.',
                'url' => route('trips.category', array_filter(['type' => 'open-trip', 'q' => $rawQuery ?: null])),
            ],
            [
                'title' => 'Cari di Private Trip',
                'type' => 'private-trip',
                'badge' => 'Eksklusif & Fleksibel',
                'description' => 'Jadwal dan rute khusus keluarga / rombonganmu.',
                'url' => route('trips.category', array_filter(['type' => 'private-trip', 'q' => $rawQuery ?: null])),
            ],
            [
                'title' => 'Cari di Open PO Oleh-Oleh',
                'type' => 'souvenir',
                'badge' => 'Khas Nusantara',
                'description' => 'Produk kuliner & kerajinan autentik nusantara.',
                'url' => route('souvenirs.index', array_filter(['q' => $rawQuery ?: null])),
            ],
        ];

        return response()->json([
            'query' => $rawQuery,
            'trips' => $trips,
            'destinations' => $destinations,
            'souvenirs' => $souvenirs,
            'popularDestinations' => $popularDestinations,
            'quickCategories' => $quickCategories,
        ]);
    }

    public function catalog(Request $request): Response
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['open-trip', 'private-trip'])],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $trips = Trip::with('vendor:id,name')
            ->withAvg(['reviews as reviews_avg_rating' => fn ($query) => $query->where('status', 'published')], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($query) => $query->where('status', 'published')])
            ->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
            ->where('departure_date', '>=', today()->toDateString())
            ->when($filters['q'] ?? null, fn ($query, $term) => $this->applySmartTripSearch($query, $term))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['date'] ?? null, fn ($query, $date) => $query->where('departure_date', $date))
            ->when($filters['guests'] ?? null, fn ($query, $guests) => $query->whereRaw('(capacity - COALESCE(reserved_seats, 0)) >= ?', [(int) $guests]))
            ->latest('id')->paginate(8, ['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'meeting_point', 'image_url', 'departure_date', 'end_date', 'capacity', 'reserved_seats', 'price', 'experience'])->withQueryString();

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
            'logo' => '/Assets/Images/logo-vendor/logo-brenggo-tour.webp',
            'city' => 'Malang, Jawa Timur',
            'tagline' => 'Spesialis Bromo, Kawah Ijen & Eksplorasi Eksotis Jawa Timur',
        ],
    ];

    public function partner(Request $request, string $partner): Response
    {
        $partnerData = self::$popularPartners[$partner] ?? null;
        $vendorRecord = null;
        if ($partnerData) {
            $vendorRecord = Vendor::where('status', 'verified')->where('name', $partnerData['name'])->first();
        } else {
            $vendorRecord = Vendor::where('status', 'verified')->where(function ($q) use ($partner) {
                $q->where('name', 'like', '%'.$partner.'%')
                    ->orWhere('id', is_numeric($partner) ? (int) $partner : -1);
            })->first();
            if ($vendorRecord) {
                $partnerData = [
                    'id' => Str::slug($vendorRecord->name),
                    'name' => $vendorRecord->name,
                    'logo' => $vendorRecord->logo_url ?? '/Assets/Images/logo-vendor/default.webp',
                    'city' => $vendorRecord->city ?? 'Indonesia',
                    'tagline' => $vendorRecord->description ?? 'Mitra Resmi Terverifikasi TapakLokal',
                ];
            }
        }
        abort_unless($partnerData, 404);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['open-trip', 'private-trip'])],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $trips = Trip::with('vendor:id,name')
            ->withAvg(['reviews as reviews_avg_rating' => fn ($query) => $query->where('status', 'published')], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($query) => $query->where('status', 'published')])
            ->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
            ->where('vendor_id', $vendorRecord?->id)
            ->where('departure_date', '>=', today()->toDateString())
            ->when($filters['q'] ?? null, fn ($query, $term) => $this->applySmartTripSearch($query, $term))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['date'] ?? null, fn ($query, $date) => $query->where('departure_date', $date))
            ->when($filters['guests'] ?? null, fn ($query, $guests) => $query->whereRaw('(capacity - COALESCE(reserved_seats, 0)) >= ?', [(int) $guests]))
            ->latest('id')->paginate(8, ['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'meeting_point', 'image_url', 'departure_date', 'end_date', 'capacity', 'reserved_seats', 'price', 'experience'])->withQueryString();

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

        $trips = Trip::with('vendor:id,name')
            ->withAvg(['reviews as reviews_avg_rating' => fn ($query) => $query->where('status', 'published')], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($query) => $query->where('status', 'published')])
            ->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
            ->where('type', $type)
            ->where('departure_date', '>=', today()->toDateString())
            ->when($filters['q'] ?? null, fn ($query, $term) => $this->applySmartTripSearch($query, $term))
            ->when($filters['date'] ?? null, fn ($query, $date) => $query->where('departure_date', $date))
            ->when($filters['guests'] ?? null, fn ($query, $guests) => $query->whereRaw('(capacity - COALESCE(reserved_seats, 0)) >= ?', [(int) $guests]))
            ->latest('id')->paginate(8, ['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'meeting_point', 'image_url', 'departure_date', 'end_date', 'capacity', 'reserved_seats', 'price', 'experience'])->withQueryString();

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
        $record = Trip::with('vendor:id,name')
            ->where('type', $tripType)
            ->where('slug', $trip)
            ->where('status', 'published')
            ->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
            ->first();

        if (! $record) {
            $record = Trip::with('vendor:id,name')
                ->where('slug', $trip)
                ->where('status', 'published')
                ->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
                ->first();
        }

        abort_unless($record, 404);

        return Inertia::render('TripDetail', [
            'tripType' => $tripType,
            'trip' => $trip,
            'tripData' => $record,
            'relatedTrips' => Trip::where('vendor_id', $record->vendor_id)->where('id', '!=', $record->id)
                ->where('status', 'published')->whereDate('departure_date', '>=', today())
                ->orderBy('departure_date')->limit(4)->get(['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'image_url', 'price']),
            'canBook' => $record->departure_date->greaterThanOrEqualTo(today()) && $record->capacity > $record->reserved_seats,
            'bookingKey' => (string) Str::uuid(),
            'virtualTours' => $record ? VirtualTour::visible()->where('trip_id', $record->id)->orderBy('position')->orderBy('id')->limit(12)->get()->map(fn ($tour) => $tour->presentation()) : [],
            'reviews' => $record ? Review::with('user:id,name')->where('trip_id', $record->id)->where('status', 'published')->latest('id')->limit(10)->get(['id', 'user_id', 'rating', 'body', 'vendor_response', 'created_at']) : [],
        ]);
    }

    public function store(BookingRequest $request, BookingService $service): RedirectResponse
    {
        $booking = $service->create($request->user(), $request->validated());

        return $request->boolean('checkout_flow') ? to_route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]) : to_route('bookings.show', $booking);
    }

    public function show(Request $request, Booking $booking, BookingService $service): Response
    {
        app(CorporateService::class)->authorizeBooking($request->user(), $booking);

        if ($booking->status === 'awaiting_payment' && $booking->expires_at->isPast()) {
            $booking = $service->transition($booking, 'expired');
        }

        $booking->load(['trip', 'vendor:id,name,phone', 'payment', 'refund']);
        $vendorLogo = collect(self::$popularPartners)->first(fn (array $partner) => strcasecmp($partner['name'], (string) $booking->vendor?->name) === 0)['logo'] ?? null;

        $ticketUrl = in_array($booking->status, ['paid', 'confirmed', 'ongoing'], true) && $booking->payment?->status === 'paid'
            ? URL::temporarySignedRoute('vendor.tickets.show', $booking->trip->end_date->copy()->endOfDay()->addDay(), ['booking' => $booking->id]) : null;

        return Inertia::render('Booking', ['booking' => $booking, 'ticketUrl' => $ticketUrl, 'vendorLogo' => $vendorLogo, 'gatewayReady' => (bool) config('platform.midtrans_server_key')]);
    }

    public function cancel(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        app(CorporateService::class)->authorizeBooking($request->user(), $booking);
        if ($corporate = $booking->corporateRequest) {
            return to_route('corporate.workspace', $corporate->corporate_company_id)->with('error', 'Buka detail kegiatan di workspace untuk membatalkan pengajuan dan reservasi perusahaan.');
        }
        $service->transition($booking, 'cancelled');

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function refund(Request $request, Booking $booking, AuditService $audit): RedirectResponse
    {
        app(CorporateService::class)->authorizeBooking($request->user(), $booking);
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
