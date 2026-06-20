<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\Product;

class Breadcrumbs extends Component
{
    public $breadcrumbs = [];

    public function mount()
    {
        $this->generateBreadcrumbs();
    }

    private function generateBreadcrumbs()
    {
        $this->breadcrumbs = [];

        // Home always comes first
        $this->breadcrumbs[] = [
            'label' => 'Acasă',
            'url' => route('home'),
        ];

        // Get the current route name and parameters
        $routeName = Route::currentRouteName();
        $routeParams = request()->route()->parameters();

        // If category page
        if ($routeName === 'products.category' && isset($routeParams['slug'])) {
            $category = Category::where('slug', $routeParams['slug'])->first();
            if ($category) {
                $this->addCategoryBreadcrumbs($category);
            }
        }

        // If product page
        if ($routeName === 'product.show' && isset($routeParams['slug'])) {

            $product = Product::where('slug', $routeParams['slug'])->first();
            if ($product && $product->category) {
                $this->addCategoryBreadcrumbs($product->category);

                // Add current product
                $this->breadcrumbs[] = [
                    'label' => $product->name,
                    'url' => route('product.show', ['slug' => $product->slug]),
                ];
            }
        }
    }

    private function addCategoryBreadcrumbs($category)
    {
        $categories = [];

        // Fetch all ancestors
        while ($category) {
            array_unshift($categories, [
                'label' => $category->name,
                'url' => route('products.category', ['slug' => $category->slug]),
            ]);
            $category = $category->parent; // Move up the hierarchy
        }

        $this->breadcrumbs = array_merge($this->breadcrumbs, $categories);
    }

    public function render()
    {
        return view('livewire.breadcrumbs');
    }
}
