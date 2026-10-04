<?php

namespace App\Services;

use App\Models\ContentPage;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\Review;
use App\Models\Trip;
use App\Models\VirtualTour;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PublicContentService
{
    public const VERSION_KEY = 'public-content:version';

    public function published(string $type): Builder
    {
        return ContentPage::where('type', $type)->where('status', 'published')
            ->where(fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /** @return array<string, mixed> */
    public function homepage(): array
    {
        $version = Cache::rememberForever(self::VERSION_KEY, fn () => (string) Str::uuid());

        return Cache::remember('public-content:home:'.$version, 60, fn () => [
            'cmsPartners' => Partner::where('status', 'published')->orderBy('position')->orderBy('id')->limit(30)->get(['id', 'name', 'image_url', 'website_url'])->toArray(),
            'cmsFaqs' => Faq::where('status', 'published')->orderBy('position')->orderBy('id')->limit(30)->get(['id', 'question', 'answer'])->toArray(),
            'cmsSections' => $this->published('homepage')->orderBy('position')->orderBy('id')->limit(10)->get(['id', 'title', 'slug', 'excerpt', 'image_url'])->toArray(),
            'cmsArticles' => $this->published('blog')->orderBy('position')->orderBy('id')->limit(6)->get(['id', 'title', 'slug', 'category', 'excerpt', 'image_url', 'metadata', 'published_at'])->toArray(),
            'cmsDestinations' => $this->published('destination')->orderBy('position')->orderBy('id')->limit(7)->get(['id', 'title', 'slug', 'excerpt', 'image_url'])->toArray(),
            'destinationTrips' => $this->destinationTrips(),
            'cmsTestimonials' => $this->published('testimonial')->orderBy('position')->orderBy('id')->limit(8)->get()->map(fn ($page) => ['id' => 'cms-'.$page->id, 'user' => ['name' => $page->title], 'trip' => ['title' => $page->excerpt], 'body' => $page->body, 'rating' => 5, 'highlight' => $page->metadata['highlight'] ?? '', 'demo' => true])->values()->all(),
            'virtualTours' => VirtualTour::visible()->where('placement', 'homepage')->orderBy('position')->orderBy('id')->get()->map(fn ($tour) => $tour->presentation())->values()->all(),
            'featuredTrips' => $this->featuredTrips(),
            'brenggoTrips' => $this->featuredTrips('BRENGGO.ID'),
            'travelerReviews' => Review::with(['user:id,name', 'trip:id,title'])->where('status', 'published')->whereHas('trip', fn ($query) => $query->where('status', 'published')->whereHas('vendor', fn ($vendor) => $vendor->where('status', 'verified')))->latest('id')->limit(8)->get(['id', 'user_id', 'trip_id', 'rating', 'body'])->toArray(),
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function featuredTrips(?string $vendorName = null): array
    {
        $images = app(TripImageService::class);

        return Trip::with('vendor:id,name')
            ->withAvg(['reviews as reviews_avg_rating' => fn ($query) => $query->where('status', 'published')], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($query) => $query->where('status', 'published')])
            ->where('status', 'published')
            ->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
            ->when($vendorName, fn (Builder $query) => $query
                ->whereHas('vendor', fn (Builder $vendor) => $vendor->where('name', $vendorName)))
            ->where('departure_date', '>=', today()->toDateString())
            ->when($vendorName === 'BRENGGO.ID', fn (Builder $query) => $query->orderByRaw("CASE WHEN slug IN ('brenggo-bromo-sunrise-open-trip', 'brenggo-kawah-ijen-blue-fire-private') THEN 0 ELSE 1 END"))
            ->latest('id')
            ->limit(12)
            ->get(['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'image_url', 'departure_date', 'end_date', 'capacity', 'reserved_seats', 'price'])
            ->map(function (Trip $trip) use ($images): array {
                return [
                    ...$trip->toArray(),
                    'thumbnail_url' => $images->thumbnailUrl($trip),
                    'photo_credit' => $trip->experience['photo_credit'] ?? null,
                ];
            })
            ->all();
    }

    /**
     * Build the homepage destination cards from trips that travelers can book now.
     *
     * @return array<int, array<string, int|string|null>>
     */
    private function destinationTrips(): array
    {
        return Trip::query()
            ->where('status', 'published')
            ->whereHas('vendor', fn (Builder $query) => $query->where('status', 'verified'))
            ->whereDate('departure_date', '>=', today())
            ->orderBy('departure_date')
            ->orderBy('id')
            ->get(['id', 'type', 'destination', 'image_url'])
            ->groupBy(fn (Trip $trip): string => trim(Str::before($trip->destination, ',')))
            ->map(function ($trips, string $destination): array {
                $openTrips = $trips->where('type', 'open-trip')->values();
                $privateTrips = $trips->where('type', 'private-trip')->values();
                $primaryTrips = $openTrips->isNotEmpty() ? $openTrips : $privateTrips;
                /** @var Trip $primaryTrip */
                $primaryTrip = $primaryTrips->first();
                $type = (string) $primaryTrip->type;

                return [
                    'id' => 'destination-'.Str::slug($destination),
                    'name' => $destination,
                    'image_url' => $primaryTrip->image_url,
                    'trip_count' => $primaryTrips->count(),
                    'trip_type' => $type,
                    'url' => route('trips.category', ['type' => $type, 'q' => $destination]),
                ];
            })
            ->sortByDesc('trip_count')
            ->take(7)
            ->values()
            ->all();
    }
}
