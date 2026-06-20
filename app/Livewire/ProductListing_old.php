<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\AttributeValue;

class ProductListing_old extends Component
{
    use WithPagination;

    public $category;
    public $categoryName;
    public $categoryIds = [];
    public $selectedOferte = false;
    public $selectedSize = null;
    public $selectedColor = null;
    public $selectedMaterial = null;
    public $availableSizes = [];
    public $availableColors = [];
    public $availableMaterials = [];
    public $totalProducts = 0;
    public $appliedFilters = [];

    protected $queryString = [
        'selectedOferte' => ['except' => null],
        'selectedSize'   => ['except' => null],
        'selectedColor'  => ['except' => null],
        'selectedMaterial'=> ['except' => null],
    ];

    public function mount($categorySlug)
    {
        $this->category = Category::where('slug', $categorySlug)->firstOrFail();
        $this->categoryIds = $this->getDescendantCategories($this->category->id);
        $this->categoryName = $this->category->name;
        $this->updateAvailableFilters();
    }

    // Recursively get descendant categories
    private function getDescendantCategories($categoryId)
    {
        $categories = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        foreach ($categories as $childId) {
            $categories = array_merge($categories, $this->getDescendantCategories($childId));
        }
        return array_merge([$categoryId], $categories);
    }

    // Trigger filtering when any filter changes
    public function updated($propertyName)
    {

        if (in_array($propertyName, ['selectedOferte', 'selectedSize', 'selectedColor', 'selectedMaterial'])) {
            //dd($propertyName);
            $this->applyFilters();
            $cleanData = $this->getCleanUrlWithParams();
            $this->dispatch('updateBrowserHistory', $cleanData);
        }
    }

    // Build a clean URL (omitting keys with empty values)
    protected function getCleanUrlWithParams()
    {
        $params = [];
        if ($this->selectedOferte) {
            $params['selectedOferte'] = $this->selectedOferte;
        }
        if ($this->selectedSize) {
            $params['selectedSize'] = $this->selectedSize;
        }
        if ($this->selectedColor) {
            $params['selectedColor'] = $this->selectedColor;
        }
        if ($this->selectedMaterial) {
            $params['selectedMaterial'] = $this->selectedMaterial;
        }
        $base = route('products.category', $this->category->slug);
        $url = count($params) ? $base . '?' . http_build_query($params) : $base;
        return ['url' => $url, 'params' => $params];
    }

    public function applyFilters()
    {
        $this->resetPage();
        $this->updateAvailableFilters();
    }

    public function resetFilters()
    {
        $this->selectedOferte = false;
        $this->selectedSize = null;
        $this->selectedColor = null;
        $this->selectedMaterial = null;
        $this->applyFilters();
    }

    public function resetOferte()
    {
        $this->selectedOferte = false;
        $this->applyFilters();
    }

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

        if ($this->selectedSize) {
            $query->whereHas('variations.attributeValues', function ($q) {
                $q->where('value', $this->selectedSize);
            });
        }

        if ($this->selectedColor) {
            $query->whereHas('variations.attributeValues', function ($q) {
                $q->where('value', $this->selectedColor);
            });
        }

        if ($this->selectedMaterial) {
            $query->whereHas('variations.attributeValues', function ($q) {
                $q->where('value', $this->selectedMaterial);
            });
        }

        $filteredProducts = $query->get();
        $this->totalProducts = $filteredProducts->count();
        $this->availableSizes = $this->extractAvailableAttributes($filteredProducts, 'Mărime');
        $this->availableColors = $this->extractAvailableAttributes($filteredProducts, 'Culoare');
        $this->availableMaterials = $this->extractAvailableAttributes($filteredProducts, 'Material');
        $this->updateAppliedFilters();
    }

    private function extractAvailableAttributes($products, $attributeName)
    {
        return AttributeValue::whereHas('productVariations', function ($q) use ($products) {
            $q->whereIn('product_id', $products->pluck('id'));
        })->whereHas('attribute', function ($q) use ($attributeName) {
            $q->where('name', $attributeName);
        })->pluck('value')->unique()->values();
    }

    private function updateAppliedFilters()
    {
        $this->appliedFilters = [];
        if ($this->selectedOferte) {
            $this->appliedFilters[] = 'Oferte: Da';
        }
        if ($this->selectedSize) {
            $this->appliedFilters[] = "Mărime: {$this->selectedSize}";
        }
        if ($this->selectedColor) {
            $this->appliedFilters[] = "Culoare: {$this->selectedColor}";
        }
        if ($this->selectedMaterial) {
            $this->appliedFilters[] = "Material: {$this->selectedMaterial}";
        }
    }

    public function render()
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

        if ($this->selectedSize) {
            $query->whereHas('variations.attributeValues', function ($q) {
                $q->where('value', $this->selectedSize);
            });
        }

        if ($this->selectedColor) {
            $query->whereHas('variations.attributeValues', function ($q) {
                $q->where('value', $this->selectedColor);
            });
        }

        if ($this->selectedMaterial) {
            $query->whereHas('variations.attributeValues', function ($q) {
                $q->where('value', $this->selectedMaterial);
            });
        }

        return view('livewire.product-listing', [
            'products' => $query->orderBy('id', 'desc')->paginate(16),
        ]);
    }
}





