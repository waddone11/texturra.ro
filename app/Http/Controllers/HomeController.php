<?php

namespace App\Http\Controllers;

use App\Models\EmagCategory;
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
        // Define parent category IDs
        $parentCategories = range(1, 36);

        $products = collect();
        $productIds = [];

        // Fetch top-level categories
        $topCategories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('id', 'asc')
            ->get();

        // Build color name → css_code map
        $colorMap = Color::all()
            ->mapWithKeys(fn($color) => [Helpers::normalize($color->name) => $color->cod_css])
            ->toArray();

        foreach ($parentCategories as $parentId) {
            // Get all descendant category IDs for the parent, plus the parent itself
            $descendantIds = $this->getDescendantCategoryIds($parentId);
            $descendantIds[] = $parentId;

            // Get random products for this category tree
            $someProds = Product::with([
                'variations.attributeValues.attribute'
            ])
                ->whereIn('category_id', $descendantIds)
                ->whereNotIn('id', $productIds)
                ->inRandomOrder()
                ->take(6)
                ->get();

            // Enrich each product with colors_with_css
            $someProds->each(function ($product) use ($colorMap) {
                $colorValues = collect($product->variations)
                    ->flatMap(fn($variation) => $variation->attributeValues)
                    ->filter(fn($av) => optional($av->attribute)->name === 'Culoare')
                    ->pluck('value')
                    ->unique();

                $product->colors_with_css = $colorValues->map(function ($colorName) use ($colorMap) {
                    $normalized = Helpers::normalize($colorName);
                    return [
                        'name' => $colorName,
                        'css' => $colorMap[$normalized] ?? '#999',
                    ];
                });
            });

            // Track used product IDs
            $productIds = array_merge($productIds, $someProds->pluck('id')->toArray());

            // Add to final product collection
            $products = $products->merge($someProds);
        }

        $colorGroups = ColorGroup::orderBy('name')->get();

        return view('home', [
            'products' => $products,
            'topCategories' => $topCategories,
            'colorGroups' => $colorGroups,
        ]);
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


    /**
     * Recursively build the category tree.
     */
    private function buildCategoryTree($categories, $parentId)
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category->emag_parent_id == $parentId) {
                $children = $this->buildCategoryTree($categories, $category->emag_id);
                $tree[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'children' => $children,
                ];
            }
        }
        return $tree;
    }

    /**
     * Recursively fetch all descendant category IDs.
     */
    private function getAllDescendantCategoryIds($emagCategoryId)
    {
        $categoryIds = [$emagCategoryId];
        $childCategories = EmagCategory::where('emag_parent_id', $emagCategoryId)->get();

        foreach ($childCategories as $childCategory) {
            $categoryIds = array_merge($categoryIds, $this->getAllDescendantCategoryIds($childCategory->emag_id));
        }

        return $categoryIds;
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
