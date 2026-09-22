<?php

namespace App\Services;

use App\Models\{ContentPage, Faq, Partner};
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
        return Cache::remember('public-content:home:'.$version, 30, fn () => [
            'cmsPartners' => Partner::where('status', 'published')->orderBy('position')->orderBy('id')->limit(30)->get(['id', 'name', 'image_url', 'website_url']),
            'cmsFaqs' => Faq::where('status', 'published')->orderBy('position')->orderBy('id')->limit(30)->get(['id', 'question', 'answer']),
            'cmsSections' => $this->published('homepage')->orderBy('position')->orderBy('id')->limit(10)->get(['id', 'title', 'slug', 'excerpt', 'image_url']),
            'cmsArticles' => $this->published('blog')->latest('id')->limit(6)->get(['id', 'title', 'slug', 'excerpt', 'image_url', 'published_at']),
        ]);
    }
}
