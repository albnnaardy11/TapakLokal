<?php

namespace App\Http\Controllers;

use App\Services\HelpCenterService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HelpCenterController extends Controller
{
    public function __construct(private HelpCenterService $helpCenter) {}

    /**
     * Display the main Help Center landing page.
     */
    public function index(Request $request): Response
    {
        $search = $request->query('q');
        $categories = $this->helpCenter->getCategories();
        $popularArticles = $this->helpCenter->getPopularArticles(null, 5);
        $allArticles = $this->helpCenter->getArticles(null, $search);

        return Inertia::render('HelpCenter', [
            'categories' => $categories,
            'popularArticles' => $popularArticles,
            'allArticles' => $allArticles,
            'initialQuery' => $search ?? '',
        ]);
    }

    /**
     * Display a specific category Help Center page (matching Traveloka Reference).
     */
    public function category(Request $request, string $category): Response
    {
        $cat = $this->helpCenter->getCategory($category);

        if (! $cat) {
            abort(404);
        }

        $search = $request->query('q');
        $popularArticles = $this->helpCenter->getPopularArticles($cat['id'], 5);
        $groupedSubcategories = $this->helpCenter->getArticlesGroupedBySubcategory($cat['id']);
        $allCategoryArticles = $this->helpCenter->getArticles($cat['id'], $search);

        return Inertia::render('HelpCategory', [
            'category' => $cat,
            'popularArticles' => $popularArticles,
            'subcategories' => $groupedSubcategories,
            'articles' => $allCategoryArticles,
            'initialQuery' => $search ?? '',
        ]);
    }

    /**
     * Display a dedicated article detail page.
     */
    public function article(Request $request, string $category, string $slug): Response
    {
        $cat = $this->helpCenter->getCategory($category);
        $article = $this->helpCenter->getArticle($category, $slug);

        if (! $article) {
            abort(404);
        }

        if (! $cat) {
            $cat = $this->helpCenter->getCategory($article['category']) ?? [
                'id' => $article['category'],
                'slug' => $article['category'],
                'name' => $article['categoryLabel'],
                'shortName' => $article['categoryLabel'],
                'badge' => 'Panduan',
            ];
        }

        $relatedArticles = $this->helpCenter->getRelatedArticles($cat['id'], $article['slug'], 4);

        return Inertia::render('HelpDetail', [
            'category' => $cat,
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
