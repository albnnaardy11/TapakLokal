<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;
use App\Models\SouvenirProduct;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SitemapController extends Controller
{
    public function robots(): Response
    {
        return response("User-agent: *\nDisallow: /admin\nDisallow: /vendor\nDisallow: /account\nDisallow: /bookings\nDisallow: /support\nDisallow: /oleh-oleh/keranjang\nDisallow: /oleh-oleh/pesanan\nSitemap: ".route('sitemap.index')."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function index(): StreamedResponse
    {
        $ranges = [];
        foreach (['products', 'trips', 'content'] as $type) {
            $ranges[$type] = (int) ceil(($this->query($type)->max('id') ?? 0) / 1000);
            abort_if($ranges[$type] > 49999, 503);
        }
        abort_if(array_sum($ranges) > 49999, 503);

        return response()->stream(function () use ($ranges): void {
            echo '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            echo '<sitemap><loc>'.$this->escape(route('sitemap.page', ['type' => 'static', 'part' => 1])).'</loc></sitemap>';
            foreach ($ranges as $type => $count) {
                for ($part = 1; $part <= $count; $part++) {
                    echo '<sitemap><loc>'.$this->escape(route('sitemap.page', compact('type', 'part'))).'</loc></sitemap>';
                }
            }
            echo '</sitemapindex>';
        }, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function page(string $type, int $part): StreamedResponse
    {
        abort_unless($part >= 1 && $part <= 49999, 404);
        abort_if($type === 'static' && $part !== 1, 404);

        return response()->stream(function () use ($type, $part): void {
            echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            if ($type === 'static') {
                foreach (['home', 'open.preorder', 'souvenirs.index', 'blog', 'discount', 'help.index', 'business.partner', 'business.corporate', 'business.affiliate', 'points.guide', 'accessibility.guide'] as $name) {
                    echo '<url><loc>'.$this->escape(route($name)).'</loc><changefreq>daily</changefreq><priority>0.8</priority></url>';
                }
                echo '<url><loc>'.$this->escape(route('trips.category', ['type' => 'open-trip'])).'</loc><changefreq>hourly</changefreq><priority>0.9</priority></url>';
                echo '<url><loc>'.$this->escape(route('trips.category', ['type' => 'private-trip'])).'</loc><changefreq>hourly</changefreq><priority>0.9</priority></url>';
            } else {
                $columns = $type === 'products' ? ['id', 'slug', 'updated_at'] : ['id', 'slug', 'type', 'updated_at'];
                foreach ($this->query($type)->whereBetween('id', [($part - 1) * 1000 + 1, $part * 1000])->select($columns)->lazyById(100) as $item) {
                    $name = match ($type) {
                        'products' => 'souvenirs.show', 'trips' => 'trips.show', default => 'content.show'
                    };
                    $parameters = $type === 'trips' ? ['tripType' => $item->type, 'trip' => $item->slug] : $item->slug;
                    echo '<url><loc>'.$this->escape(route($name, $parameters)).'</loc><lastmod>'.$item->updated_at->toAtomString().'</lastmod></url>';
                }
            }
            echo '</urlset>';
        }, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function query(string $type): Builder
    {
        return match ($type) {
            'products' => SouvenirProduct::published(),
            'trips' => Trip::where('status', 'published')->whereIn('type', ['open-trip', 'private-trip'])->whereHas('vendor', fn ($q) => $q->where('status', 'verified'))->whereDate('departure_date', '>=', today()),
            'content' => ContentPage::where('status', 'published')->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now())),
            default => abort(404),
        };
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
