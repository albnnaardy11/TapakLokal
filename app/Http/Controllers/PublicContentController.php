<?php

namespace App\Http\Controllers;

use App\Services\PublicContentService;
use Illuminate\Http\Request;
use Inertia\{Inertia, Response};

class PublicContentController extends Controller
{
    public function __construct(private PublicContentService $content) {}

    public function home(): Response
    {
        return Inertia::render('Welcome', [
            'appName' => config('app.name', 'TapakLokal'), ...$this->content->homepage(),
            'featuredTrips' => \App\Models\Trip::with('vendor:id,name')->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))->where('departure_date', '>=', today()->toDateString())->orderBy('departure_date')->orderBy('id')->limit(6)->get(['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'image_url', 'departure_date', 'capacity', 'reserved_seats', 'price']),
            'travelerReviews' => \App\Models\Review::with(['user:id,name', 'trip:id,title'])->where('status', 'published')->whereHas('trip', fn ($query) => $query->where('status', 'published')->whereHas('vendor', fn ($vendor) => $vendor->where('status', 'verified')))->latest('id')->limit(8)->get(['id', 'user_id', 'trip_id', 'rating', 'body']),
        ]);
    }

    public function blog(): Response
    {
        return Inertia::render('Blog', ['publishedArticles' => $this->content->published('blog')->latest('id')->paginate(12, ['id', 'title', 'slug', 'excerpt', 'image_url', 'published_at'])]);
    }

    public function explore(Request $request, string $type): Response
    {
        $labels = ['destination' => 'Destinasi', 'hidden-gem' => 'Hidden Gems', 'culinary' => 'Kuliner Lokal', 'souvenir' => 'Oleh-oleh'];
        abort_unless(isset($labels[$type]), 404);
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $items = $this->content->published($type)->when($data['q'] ?? null, fn ($query, $term) => $query->where('title', 'like', '%'.$term.'%'))->orderBy('position')->orderBy('id')->paginate(12, ['id', 'title', 'slug', 'excerpt', 'image_url'])->withQueryString();
        return Inertia::render('Explore', ['title' => $labels[$type], 'items' => $items, 'type' => $type, 'query' => $data['q'] ?? '']);
    }
}
