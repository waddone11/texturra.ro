<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Support\Facades\Response;
use App\Models\ColorGroup;
use App\Models\Color;
use App\Helpers\Helpers;

class HomeController extends Controller
{
    public function index()
    {
        // The 2026 redesign is the homepage. (The HOMEPAGE_VERSION old/new switch was
        // removed once the redesign shipped; home-old.blade.php is kept only as a backup.)
        return view('home-new', $this->homeData());
    }

    /**
     * Build the data shared by every homepage version
     * (products with color swatches, top categories, color groups).
     */
    private function homeData(): array
    {
        // Define parent category IDs
        $parentCategories = range(1, 36);

        $products = collect();
        $productIds = [];

        // Fetch top-level categories
        $topCategories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($parentCategories as $parentId) {
            // Get all descendant category IDs for the parent, plus the parent itself
            $descendantIds = $this->getDescendantCategoryIds($parentId);
            $descendantIds[] = $parentId;

            // Get random products for this category tree
            $someProds = Product::with(['colors', 'materials'])
                ->whereIn('category_id', $descendantIds)
                ->whereNotIn('id', $productIds)
                ->inRandomOrder()
                ->take(6)
                ->get();

            // Color swatches from the product_color pivot (name + cod_css straight
            // from Color) — same {name, css} shape the views consume, no name-map lookup.
            $someProds->each(function ($product) {
                $product->colors_with_css = $product->colors
                    ->map(fn ($color) => ['name' => $color->name, 'css' => $color->cod_css])
                    ->values();
            });

            // Track used product IDs
            $productIds = array_merge($productIds, $someProds->pluck('id')->toArray());

            // Add to final product collection
            $products = $products->merge($someProds);
        }

        // Eager-load each group's colors so the palette selector (section 5) can use the
        // first color's cod_css as a guaranteed swatch fallback behind the .avif texture.
        $colorGroups = ColorGroup::with('colors')->orderBy('name')->get();

        // Section 4 (homepage redesign): the newest ACTIVE products for the "Noutăți" band.
        // Replaces the GPT-invented "collections" with real, latest catalogue entries.
        $newestProducts = Product::with(['category', 'colors', 'materials'])
            ->where('status', 1)
            ->orderByDesc('id')
            ->take(4)
            ->get();

        $newestProducts->each(function ($product) {
            $product->colors_with_css = $product->colors
                ->map(fn ($color) => ['name' => $color->name, 'css' => $color->cod_css])
                ->values();
        });

        return [
            'products' => $products,
            'topCategories' => $topCategories,
            'colorGroups' => $colorGroups,
            'newestProducts' => $newestProducts,
        ];
    }


    /**
     * Recursively get descendant category IDs.
     */
    private function getDescendantCategoryIds($parentId)
    {
        $descendants = \App\Models\Category::where('parent_id', $parentId)->pluck('id')->toArray();
        foreach ($descendants as $childId) {
            $descendants = array_merge($descendants, $this->getDescendantCategoryIds($childId));
        }
        return $descendants;
    }



    public function about()
    {
        return view('about');
    }

    public function politicaLivrare()
    {
        return view('politica-livrare');
    }

    public function politicaRetur()
    {
        return view('politica-retur');
    }

    public function politicaConfidentialitate()
    {
        return view('politica-confidentialitate');
    }

    public function politicaGdpr()
    {
        return view('politica-gdpr');
    }

    public function termeniConditii()
    {
        return view('termeni-conditii');
    }


    /**
     * Generate an XML sitemap that includes:
     * - The home page
     * - Static pages (about, politica-livrare, etc.)
     * - All categories
     * - All products
     */
    public function sitemap()
    {
        $urls = [];

        // Base URL (adjust if you have a config('app.url') defined)
        $baseUrl = url('/');

        // 1. Home page and static pages
        $staticRoutes = [
            $baseUrl,
            route('about'),
            route('politica-livrare'),
            route('politica-retur'),
            route('politica-confidentialitate'),
            route('politica-gdpr'),
            route('termeni-conditii'),
        ];
        foreach ($staticRoutes as $url) {
            $urls[] = [
                'loc' => $url,
                'lastmod' => Carbon::now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '1.0',
            ];
        }

        // 2. All categories
        $categories = Category::where('status', 1)->get();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('products.category', ['slug' => $category->slug]),
                'lastmod' => $category->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // 3. All products
        $products = Product::all();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('product.show', ['slug' => $product->slug]),
                'lastmod' => $product->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // Build XML content
        $xml = view('sitemap', ['urls' => $urls])->render();

        // Return response with XML header
        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }





}
