<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Trip;
use App\Services\PublicContentService;
use App\Services\TripImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicContentController extends Controller
{
    public function __construct(private PublicContentService $content) {}

    public function home(): Response
    {
        return Inertia::render('Welcome', [
            'appName' => config('app.name', 'TapakLokal'),
            ...$this->content->homepage(),
        ]);
    }

    public function tripThumbnail(Trip $trip, TripImageService $images): StreamedResponse
    {
        abort_unless($trip->status === 'published' && $trip->vendor()->where('status', 'verified')->exists(), 404);

        $path = $images->thumbnail($trip);
        abort_unless($path, 404);

        return Storage::disk('public')->response($path, 'trip-thumbnail.webp', [
            'Cache-Control' => 'public, max-age=3600',
            'Content-Type' => 'image/webp',
        ]);
    }

    public function blog(): Response
    {
        return Inertia::render('Blog', ['publishedArticles' => $this->content->published('blog')->latest('id')->paginate(12, ['id', 'title', 'slug', 'category', 'excerpt', 'image_url', 'published_at'])]);
    }

    public function discount(): Response
    {
        return Inertia::render('Discount', [
            'promotions' => Promotion::where('status', 'published')
                ->whereDate('starts_at', '<=', today())
                ->whereDate('ends_at', '>=', today())
                ->whereColumn('used_count', '<', 'usage_limit')
                ->orderBy('ends_at')->limit(60)
                ->get(['id', 'name', 'code', 'type', 'value', 'minimum_amount', 'maximum_discount', 'ends_at']),
        ]);
    }

    public function explore(Request $request, string $type): Response
    {
        $labels = ['destination' => 'Destinasi', 'hidden-gem' => 'Hidden Gems', 'culinary' => 'Kuliner Lokal', 'souvenir' => 'Oleh-oleh'];
        abort_unless(isset($labels[$type]), 404);
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);
        $items = $this->content->published($type)
            ->when($data['q'] ?? null, fn ($query, $term) => $query->where(fn ($q) => $q->where('title', 'like', '%'.$term.'%')->orWhere('excerpt', 'like', '%'.$term.'%')->orWhere('body', 'like', '%'.$term.'%')))
            ->when($data['category'] ?? null, fn ($query, $category) => $query->where(fn ($q) => $q->where('category', 'like', '%'.$category.'%')->orWhere('title', 'like', '%'.$category.'%')->orWhere('excerpt', 'like', '%'.$category.'%')))
            ->orderBy('position')->orderBy('id')->paginate(12, ['id', 'title', 'slug', 'excerpt', 'image_url'])->withQueryString();

        return Inertia::render('Explore', ['title' => $labels[$type], 'items' => $items, 'type' => $type, 'query' => $data['q'] ?? '']);
    }
}
