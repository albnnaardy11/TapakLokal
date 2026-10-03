<?php

use App\Http\Controllers\Admin\SouvenirFinanceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CorporateAuthController;
use App\Http\Controllers\CorporateController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\PublicContentController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SouvenirController;
use App\Models\ContentPage;
use App\Models\VirtualTour;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get('/blog', [PublicContentController::class, 'blog'])->name('blog');
Route::get('/tapak-points', fn (): Response => Inertia::render('PointsGuide'))->name('points.guide');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap/{type}/{part}.xml', [SitemapController::class, 'page'])->whereIn('type', ['static', 'products', 'trips', 'content'])->whereNumber('part')->name('sitemap.page');
Route::get('/discount', [PublicContentController::class, 'discount'])->name('discount');
Route::get('/open-preorder', fn (): Response => Inertia::render('OpenPreorder'))->name('open.preorder');
Route::redirect('/preorder', '/open-preorder');
Route::get('/oleh-oleh', [SouvenirController::class, 'index'])->name('souvenirs.index');
Route::get('/oleh-oleh/produk/{product}', [SouvenirController::class, 'show'])->name('souvenirs.show');
Route::get('/oleh-oleh/toko/{shop}', [SouvenirController::class, 'store'])->name('souvenirs.store');
Route::middleware('auth')->prefix('oleh-oleh')->group(function () {
    Route::get('keranjang', [SouvenirController::class, 'cart'])->name('souvenirs.cart');
    Route::post('keranjang', [SouvenirController::class, 'addCart'])->middleware('throttle:30,1')->name('souvenirs.cart.add');
    Route::put('keranjang/{line}', [SouvenirController::class, 'updateCart'])->middleware('throttle:60,1')->name('souvenirs.cart.update');
    Route::post('simpan', [SouvenirController::class, 'saved'])->middleware('throttle:30,1')->name('souvenirs.saved');
    Route::post('ulasan', [SouvenirController::class, 'review'])->middleware('throttle:10,1')->name('souvenirs.review');
    Route::get('pesanan', [SouvenirController::class, 'orders'])->name('souvenirs.orders');
    Route::post('pesanan', [SouvenirController::class, 'order'])->middleware('throttle:10,1')->name('souvenirs.orders.create');
    Route::get('pesanan/{order}', [SouvenirController::class, 'orderShow'])->name('souvenirs.orders.show');
    Route::post('pesanan/{order}/bayar', [SouvenirController::class, 'pay'])->middleware('throttle:10,1')->name('souvenirs.orders.pay');
    Route::put('pesanan/{order}', [SouvenirController::class, 'transition'])->middleware('throttle:20,1')->name('souvenirs.orders.update');
});
Route::middleware(['auth', 'can:vendor.access'])->prefix('vendor/oleh-oleh')->group(function () {
    Route::get('/', [App\Http\Controllers\Vendor\SouvenirController::class, 'index'])->name('vendor.souvenirs');
    Route::post('produk', [App\Http\Controllers\Vendor\SouvenirController::class, 'save'])->name('vendor.souvenirs.create');
    Route::put('produk/{product}', [App\Http\Controllers\Vendor\SouvenirController::class, 'save'])->name('vendor.souvenirs.update');
    Route::put('pesanan/{order}', [App\Http\Controllers\Vendor\SouvenirController::class, 'transition'])->name('vendor.souvenirs.orders.update');
    Route::put('ulasan/{review}', [App\Http\Controllers\Vendor\SouvenirController::class, 'replyReview'])->name('vendor.souvenirs.reviews.reply');
});
Route::middleware(['auth', 'can:admin.access', 'can:operations.manage'])->group(function () {
    Route::get('/admin/oleh-oleh', [App\Http\Controllers\Vendor\SouvenirController::class, 'moderation'])->name('admin.souvenirs');
    Route::put('/admin/oleh-oleh/{product}', [App\Http\Controllers\Vendor\SouvenirController::class, 'moderate'])->name('admin.souvenirs.moderate');
    Route::put('/admin/ulasan-oleh-oleh/{review}', [App\Http\Controllers\Vendor\SouvenirController::class, 'moderateReview'])->name('admin.souvenirs.reviews.moderate');
});
Route::get('/explore/{type}', [PublicContentController::class, 'explore'])->name('explore');
Route::middleware(['auth', 'can:admin.access', 'can:finance.view'])->group(function () {
    Route::get('/admin/keuangan-oleh-oleh', [SouvenirFinanceController::class, 'index'])->name('admin.souvenirs.finance');
    Route::post('/admin/keuangan-oleh-oleh/{payment}/rekonsiliasi', [SouvenirFinanceController::class, 'reconcile'])->middleware(['can:refund.approve', 'throttle:20,1'])->name('admin.souvenirs.reconcile');
});

Route::get('/blog/{article}', function (string $article): Response {
    $existing = ContentPage::withTrashed()->where('slug', $article)->first();

    if ($existing) {
        if ($existing->trashed() || $existing->type !== 'blog' || $existing->status !== 'published' || ($existing->published_at && $existing->published_at->isFuture())) {
            abort(404);
        }

        return Inertia::render('BlogDetail', [
            'articleId' => $article,
            'content' => $existing,
        ]);
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

Route::get('/tapaklokal-priority/about', fn (): Response => Inertia::render('PriorityAbout'))->name('priority.about');
Route::redirect('/TapakLokal-priority/about', '/tapaklokal-priority/about');
Route::redirect('/priority', '/tapaklokal-priority/about');

Route::get('/bantuan', [HelpCenterController::class, 'index'])->name('help.index');
Route::get('/bantuan/{category}', [HelpCenterController::class, 'category'])->name('help.category');
Route::get('/bantuan/{category}/{slug}', [HelpCenterController::class, 'article'])->name('help.article');
Route::redirect('/help-center', '/bantuan');
Route::redirect('/help', '/bantuan');

Route::get('/pilihan-trip/{type}', [BookingController::class, 'tripType'])->whereIn('type', ['open-trip', 'private-trip'])->name('trips.category');
Route::get('/mitra/{partner}', [BookingController::class, 'partner'])->name('partners.show');
Route::redirect('/open-trip', '/pilihan-trip/open-trip');
Route::redirect('/private-trip', '/pilihan-trip/private-trip');

Route::get('/trips/{tripType}/{trip}', [BookingController::class, 'detail'])->whereIn('tripType', ['open-trip', 'private-trip'])->name('trips.show');
Route::get('/bisnis/mitra-vendor', fn (): Response => Inertia::render('BusinessPartner'))->name('business.partner');
Route::get('/bisnis/corporate', fn (): Response => Inertia::render('BusinessCorporate'))->name('business.corporate');
Route::get('/bisnis/affiliate', fn (): Response => Inertia::render('BusinessAffiliate'))->name('business.affiliate');
Route::redirect('/mitra-vendor', '/bisnis/mitra-vendor');
Route::redirect('/corporate', '/bisnis/corporate');
Route::redirect('/affiliate', '/bisnis/affiliate');
Route::redirect('/affiliator', '/bisnis/affiliate');

Route::get('/', [PublicContentController::class, 'home'])->name('home');

require __DIR__.'/platform.php';

Route::get('/corporate/login', [CorporateAuthController::class, 'create'])->name('corporate.login');
Route::post('/corporate/login', [CorporateAuthController::class, 'store'])->middleware('throttle:login')->name('corporate.login.store');
Route::post('/corporate/account', [CorporateAuthController::class, 'register'])->middleware(['guest', 'throttle:5,1'])->name('corporate.account.store');
Route::get('/corporate/register', [CorporateController::class, 'register'])->name('corporate.register');
Route::post('/corporate/register', [CorporateController::class, 'storeCompany'])->middleware('throttle:5,1')->name('corporate.register.store');
Route::middleware('auth')->group(function () {
    Route::get('/corporate/dashboard', [CorporateController::class, 'index'])->name('corporate.dashboard');
    Route::post('/corporate/invitations/{membership}', [CorporateController::class, 'acceptInvitation'])->name('corporate.invitation.accept');
    Route::get('/corporate/companies/{company}', [CorporateController::class, 'workspace'])->name('corporate.workspace');
    Route::post('/corporate/companies/{company}/members', [CorporateController::class, 'invite'])->middleware('throttle:10,1')->name('corporate.members.invite');
    Route::delete('/corporate/companies/{company}/members/{membership}', [CorporateController::class, 'removeMember'])->name('corporate.members.remove');
    Route::put('/corporate/companies/{company}/policy', [CorporateController::class, 'policy'])->name('corporate.policy');
    Route::post('/corporate/companies/{company}/requests', [CorporateController::class, 'storeRequest'])->middleware('throttle:20,1')->name('corporate.requests.store');
    Route::post('/corporate/requests/{corporateRequest}/decision', [CorporateController::class, 'decide'])->name('corporate.requests.decide');
    Route::post('/corporate/requests/{corporateRequest}/book', [CorporateController::class, 'book'])->middleware('throttle:10,1')->name('corporate.requests.book');
    Route::get('/corporate/companies/{company}/report', [CorporateController::class, 'report'])->middleware('throttle:5,1')->name('corporate.report');
});
Route::middleware(['auth', 'can:admin.access', 'can:operations.manage'])->group(function () {
    Route::get('/admin/corporate', [CorporateController::class, 'admin'])->name('admin.corporate');
    Route::put('/admin/corporate/companies/{company}', [CorporateController::class, 'verify'])->name('admin.corporate.verify');
    Route::post('/admin/corporate/requests/{corporateRequest}/offer', [CorporateController::class, 'offer'])->name('admin.corporate.offer');
});
Route::get('/corporate/start', fn () => to_route('corporate.register'))->middleware('auth')->name('corporate.start');
Route::patch('/corporate/requests/{corporateRequest}/travelers', [CorporateController::class, 'travelers'])->middleware('auth')->name('corporate.requests.travelers');
Route::post('/corporate/requests/{corporateRequest}/cancel', [CorporateController::class, 'cancel'])->middleware('auth')->name('corporate.requests.cancel');
