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
            'cmsPartners' => Partner::where('status', 'published')->orderBy('position')->orderBy('id')->limit(30)->get(['id', 'name', 'image_url', 'website_url']),
            'cmsFaqs' => Faq::where('status', 'published')->orderBy('position')->orderBy('id')->limit(30)->get(['id', 'question', 'answer']),
            'cmsSections' => $this->published('homepage')->orderBy('position')->orderBy('id')->limit(10)->get(['id', 'title', 'slug', 'excerpt', 'image_url']),
            'cmsArticles' => $this->published('blog')->orderBy('position')->orderBy('id')->limit(6)->get(['id', 'title', 'slug', 'category', 'excerpt', 'image_url', 'published_at']),
            'cmsDestinations' => $this->published('destination')->orderBy('position')->orderBy('id')->limit(7)->get(['id', 'title', 'slug', 'excerpt', 'image_url']),
            'cmsTestimonials' => $this->published('testimonial')->orderBy('position')->orderBy('id')->limit(8)->get()->map(fn ($page) => ['id' => 'cms-'.$page->id, 'user' => ['name' => $page->title], 'trip' => ['title' => $page->excerpt], 'body' => $page->body, 'rating' => 5, 'highlight' => $page->metadata['highlight'] ?? '', 'demo' => true]),
            'virtualTours' => VirtualTour::visible()->where('placement', 'homepage')->orderBy('position')->orderBy('id')->limit(12)->get()->map(fn ($tour) => $tour->presentation()),
            'featuredTrips' => Trip::with('vendor:id,name')->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))->where('departure_date', '>=', today()->toDateString())->orderBy('departure_date')->orderBy('id')->limit(12)->get(['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'image_url', 'departure_date', 'end_date', 'capacity', 'reserved_seats', 'price']),
            'travelerReviews' => Review::with(['user:id,name', 'trip:id,title'])->where('status', 'published')->whereHas('trip', fn ($query) => $query->where('status', 'published')->whereHas('vendor', fn ($vendor) => $vendor->where('status', 'verified')))->latest('id')->limit(8)->get(['id', 'user_id', 'trip_id', 'rating', 'body']),
        ]);
    }
}
