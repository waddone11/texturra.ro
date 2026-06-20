<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class ProductList extends Component
{
    use WithPagination;

    public $category;
    public $categoryIds = [];
    public $selectedOferte = false;
    public $selectedSize = null;
    public $selectedColor = null;
    public $selectedMaterial = null;

    protected $listeners = ['filtersUpdated' => 'applyFilters'];

    public function mount($categorySlug)
    {
        $this->category = Category::where('slug', $categorySlug)->firstOrFail();
        $this->categoryIds = $this->getDescendantCategories($this->category->id);

        // Load filters from URL parameters
        $this->selectedOferte = request()->get('oferte') === 'true';
        $this->selectedSize = request()->get('size', null);
        $this->selectedColor = request()->get('color', null);
        $this->selectedMaterial = request()->get('material', null);
    }

    private function getDescendantCategories($categoryId)
    {
        $categories = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        foreach ($categories as $childId) {
            $categories = array_merge($categories, $this->getDescendantCategories($childId));
        }
        return array_merge([$categoryId], $categories);
    }

    public function applyFilters($filters = [])
    {
        $this->selectedOferte = $filters['selectedOferte'] ?? false;
        $this->selectedSize = $filters['selectedSize'] ?? null;
        $this->selectedColor = $filters['selectedColor'] ?? null;
        $this->selectedMaterial = $filters['selectedMaterial'] ?? null;

        $this->resetPage();
    }


    public function render()
    {
        $query = Product::whereIn('category_id', $this->categoryIds);

        if ($this->selectedOferte) {
            $query->where(function ($q) {
                // Check if the product itself has a discount
                $q->whereHas('discount', function ($discountQuery) {
                    $discountQuery->where('start_date', '<=', now())->where('end_date', '>=', now());
                });

                // Check if any parent category of the product has a discount
                $q->orWhereHas('category', function ($categoryQuery) {
                    $categoryQuery->whereHas('discount', function ($discountQuery) {
                        $discountQuery->where('start_date', '<=', now())->where('end_date', '>=', now());
                    });

                    // Also check discounts in all ancestors of the category
                    $categoryQuery->orWhereHas('parent', function ($parentQuery) {
                        $parentQuery->whereHas('discount', function ($discountQuery) {
                            $discountQuery->where('start_date', '<=', now())->where('end_date', '>=', now());
                        });

                        // Recursively check all ancestors
                        $parentQuery->orWhereHas('parent.parent', function ($grandParentQuery) {
                            $grandParentQuery->whereHas('discount', function ($discountQuery) {
                                $discountQuery->where('start_date', '<=', now())->where('end_date', '>=', now());
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

        return view('livewire.product-list', [
            'products' => $query->orderBy('id', 'desc')->paginate(6)
        ]);
    }
}

