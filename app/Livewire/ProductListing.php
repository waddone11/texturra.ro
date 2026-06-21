<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\Color;
use App\Models\ColorGroup;
use App\Helpers\Helpers;

class ProductListing extends Component
{
    use WithPagination;

    public $category;
    public $categoryName;
    public $categoryIds = [];
    public $childCategories;

    // A flag for "Oferte" (special offers)
    public $selectedOferte = false;
    // Dynamic storage for selected filters: key = attribute name, value = selected value
    public $selectedFilters = [];
    // Dynamic available filters: key = attribute name, value = array of available values
    public $availableFilters = [];

    public $totalProducts = 0;
    public $appliedFilters = [];

    /**
     * For query string persistence, we predefine the most common attributes.
     * For truly dynamic attributes, you can handle URL updates manually (as done in getCleanUrlWithParams)
     */
    protected $queryString = [
        'selectedOferte' => ['except' => false],
        'selectedFilters.Material' => ['except' => null],
        'selectedFilters.Culoare' => ['except' => null],
        'selectedFilters.Diametru Top' => ['except' => null],
        'selectedFilters.Diametru Baza' => ['except' => null],
        'selectedFilters.Inaltime' => ['except' => null],
        'selectedFilters.Volum' => ['except' => null],
        'selectedFilters.Ambalare' => ['except' => null],
        'selectedFilters.Dimensiune bax (Lxlxh)' => ['except' => null],
        'selectedFilters.Domeniu' => ['except' => null],
    ];

    public function mount($categorySlug)
    {
        $this->category = Category::where('slug', $categorySlug)->firstOrFail();
        $this->categoryIds = $this->getDescendantCategories($this->category->id);
        $this->categoryName = $this->category->name;
        $this->childCategories = $this->category->children;
        $this->updateAvailableFilters();
    }

    /**
     * Recursively get descendant category IDs.
     */
    private function getDescendantCategories($categoryId)
    {
        $categories = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        foreach ($categories as $childId) {
            $categories = array_merge($categories, $this->getDescendantCategories($childId));
        }
        return array_merge([$categoryId], $categories);
    }

    /**
     * When any filter value is updated, reapply filters and update the URL.
     */
    public function updated($propertyName)
    {
        // Check if the updated property is either the offer flag or one of the dynamic selected filters.
        if ($propertyName === 'selectedOferte' || str_starts_with($propertyName, 'selectedFilters')) {
            $this->resetPage();
            $this->applyFilters();
            $cleanData = $this->getCleanUrlWithParams();
            // Dispatch a browser event to update the URL (handled on the front end)
            $this->dispatch('updateBrowserHistory', $cleanData);
        }
    }

    /**
     * Build a clean URL based on the current filters.
     */
    protected function getCleanUrlWithParams()
    {
        $params = [];
        if ($this->selectedOferte) {
            $params['selectedOferte'] = $this->selectedOferte;
        }
        foreach ($this->selectedFilters as $attribute => $value) {
            if (!empty($value)) {
                $params[$attribute] = $value;
            }
        }
        $base = route('products.category', $this->category->slug);
        $url = count($params) ? $base . '?' . http_build_query($params) : $base;
        return ['url' => $url, 'params' => $params];
    }

    /**
     * Apply filters by updating available filter options.
     */
    public function applyFilters()
    {
        $this->resetPage();
        $this->updateAvailableFilters();
    }

    /**
     * Reset all filters.
     */
    public function resetFilters()
    {
        $this->selectedOferte = false;
        $this->selectedFilters = [];
        $this->applyFilters();
    }

    public function resetOferte()
    {
        $this->selectedOferte = false;
        $this->applyFilters();
    }

    /**
     * Update the list of available filters dynamically based on the filtered products.
     */
    private function updateAvailableFilters()
    {
        $query = Product::whereIn('category_id', $this->categoryIds);

        if ($this->selectedOferte) {
            $query->where(function ($q) {
                $q->whereHas('discount', function ($discountQuery) {
                    $discountQuery->where('start_date', '<=', now())
                        ->where('end_date', '>=', now());
                })->orWhereHas('category', function ($categoryQuery) {
                    $categoryQuery->whereHas('discount', function ($discountQuery) {
                        $discountQuery->where('start_date', '<=', now())
                            ->where('end_date', '>=', now());
                    })->orWhereHas('parent', function ($parentQuery) {
                        $parentQuery->whereHas('discount', function ($discountQuery) {
                            $discountQuery->where('start_date', '<=', now())
                                ->where('end_date', '>=', now());
                        })->orWhereHas('parent.parent', function ($grandParentQuery) {
                            $grandParentQuery->whereHas('discount', function ($discountQuery) {
                                $discountQuery->where('start_date', '<=', now())
                                    ->where('end_date', '>=', now());
                            });
                        });
                    });
                });
            });
        }

        // Dynamically apply each selected filter (Culoare/Material → pivots).
        foreach ($this->selectedFilters as $attribute => $value) {
            if (!empty($value)) {
                $this->applyAttributeFilter($query, $attribute, $value);
            }
        }

        $filteredProducts = $query->with(['colors', 'materials'])->get();
        $this->totalProducts = $filteredProducts->count();

        // Build available filters from the clean pivots (Culoare + Material are the
        // only attributes that carry data; the dimensional keys are vestigial).
        $filters = [];
        foreach ($filteredProducts as $product) {
            foreach ($product->materials as $material) {
                $filters['Material'][] = $material->name;
            }
            foreach ($product->colors as $color) {
                $filters['Culoare'][] = $color->name;
            }
        }
        // Remove duplicates and sort values for each attribute.
        foreach ($filters as $attrName => $values) {
            $filters[$attrName] = collect($values)->unique()->sort()->values()->all();
        }
        $this->availableFilters = $filters;
        $this->updateAppliedFilters();
    }

    /**
     * Update the list of applied filters for display.
     */
    private function updateAppliedFilters()
    {
        $this->appliedFilters = [];
        if ($this->selectedOferte) {
            $this->appliedFilters[] = 'Oferte: Da';
        }
        foreach ($this->selectedFilters as $attribute => $value) {
            if (!empty($value)) {
                $this->appliedFilters[] = "{$attribute}: {$value}";
            }
        }
    }

    /**
     * Apply a single storefront filter to the product query. Only Culoare and
     * Material carry data; both live on clean pivots now (no more variations).
     * Unknown/vestigial attribute keys (old dimensional filters) are ignored.
     */
    private function applyAttributeFilter($query, $attribute, $value)
    {
        $relation = match ($attribute) {
            'Culoare' => 'colors',
            'Material' => 'materials',
            default => null,
        };

        if ($relation === null) {
            return;
        }

        $query->whereHas($relation, function ($q) use ($value) {
            $q->where('name', $value);
        });
    }

    public function render()
    {
        $query = Product::with(['colors', 'materials'])
        ->whereIn('category_id', $this->categoryIds);

        if ($this->selectedOferte) {
            $query->where(function ($q) {
                $q->whereHas('discount', function ($discountQuery) {
                    $discountQuery->where('start_date', '<=', now())
                        ->where('end_date', '>=', now());
                })->orWhereHas('category', function ($categoryQuery) {
                    $categoryQuery->whereHas('discount', function ($discountQuery) {
                        $discountQuery->where('start_date', '<=', now())
                            ->where('end_date', '>=', now());
                    })->orWhereHas('parent', function ($parentQuery) {
                        $parentQuery->whereHas('discount', function ($discountQuery) {
                            $discountQuery->where('start_date', '<=', now())
                                ->where('end_date', '>=', now());
                        })->orWhereHas('parent.parent', function ($grandParentQuery) {
                            $grandParentQuery->whereHas('discount', function ($discountQuery) {
                                $discountQuery->where('start_date', '<=', now())
                                    ->where('end_date', '>=', now());
                            });
                        });
                    });
                });
            });
        }

        foreach ($this->selectedFilters as $attribute => $value) {
            if (!empty($value)) {
                $this->applyAttributeFilter($query, $attribute, $value);
            }
        }

        $products = $query->orderBy('id', 'desc')->paginate(16);

        // Attach color CSS swatches from the product_color pivot (name + cod_css
        // straight from Color) — same {name, css} shape the view consumes.
        $products->getCollection()->each(function ($product) {
            $product->colors_with_css = $product->colors
                ->map(fn ($color) => ['name' => $color->name, 'css' => $color->cod_css])
                ->values();
        });

        return view('livewire.product-listing', [
            'products' => $products,
            'childCategories' => $this->childCategories,
        ]);
    }

}
