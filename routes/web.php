<?php

use App\Models\ContentPage;
use App\Models\Faq;
use App\Models\Partner;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get('/blog', [\App\Http\Controllers\PublicContentController::class, 'blog'])->name('blog');
Route::get('/explore/{type}', [\App\Http\Controllers\PublicContentController::class, 'explore'])->name('explore');

Route::get('/blog/{article}', function (string $article): Response {
    $content = ContentPage::where('slug', $article)->where('type', 'blog')->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))->first();
    if ($content) { return Inertia::render('Content', ['content' => $content]); }
    abort_unless(in_array($article, ['bali', 'islands', 'local'], true), 404);
    return Inertia::render('BlogDetail', ['articleId' => $article]);
})->name('blog.show');

Route::get('/pages/{slug}', function (string $slug): Response {
    return Inertia::render('Content', ['content' => ContentPage::where('slug', $slug)->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))->firstOrFail()]);
})->name('content.show');

Route::get('/panduan-aksesibilitas', fn (): Response => Inertia::render('AccessibilityGuide'))->name('accessibility.guide');

Route::get('/trips/{tripType}/{trip}', [\App\Http\Controllers\BookingController::class, 'detail'])->whereIn('tripType', ['open-trip', 'private-trip'])->name('trips.show');

Route::get('/', [\App\Http\Controllers\PublicContentController::class, 'home'])->name('home');

require __DIR__.'/platform.php';
