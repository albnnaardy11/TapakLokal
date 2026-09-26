<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\PublicContentController;
use App\Models\ContentPage;
use App\Models\VirtualTour;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get('/blog', [PublicContentController::class, 'blog'])->name('blog');
Route::get('/explore/{type}', [PublicContentController::class, 'explore'])->name('explore');

Route::get('/blog/{article}', function (string $article): Response {
    $existing = ContentPage::withTrashed()->where('slug', $article)->first();

    if ($existing) {
        if ($existing->trashed() || $existing->type !== 'blog' || $existing->status !== 'published' || ($existing->published_at && $existing->published_at->isFuture())) {
            abort(404);
        }

        return Inertia::render('Content', ['content' => $existing]);
    }

    if (in_array($article, ['bali', 'islands', 'local'])) {
        return Inertia::render('BlogDetail', ['articleId' => $article]);
    }

    abort(404);
})->name('blog.show');

Route::get('/pages/{slug}', function (string $slug): Response {
    $content = ContentPage::where('slug', $slug)->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))->firstOrFail();

    return Inertia::render('Content', ['content' => $content, 'virtualTours' => VirtualTour::visible()->where('content_page_id', $content->id)->orderBy('position')->orderBy('id')->limit(12)->get()->map(fn ($tour) => $tour->presentation())]);
})->name('content.show');

Route::get('/panduan-aksesibilitas', fn (): Response => Inertia::render('AccessibilityGuide'))->name('accessibility.guide');

Route::get('/trips/{tripType}/{trip}', [BookingController::class, 'detail'])->whereIn('tripType', ['open-trip', 'private-trip'])->name('trips.show');

Route::get('/', [PublicContentController::class, 'home'])->name('home');

require __DIR__.'/platform.php';
