<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Request;

class ProductFilters extends Component
{
    public $category;
    public $categoryIds = [];
    public $selectedOferte;
    public $selectedSize;
    public $selectedColor;
    public $selectedMaterial;
    public $availableSizes = [];
    public $availableColors = [];
    public $availableMaterials = [];

    public function mount($categorySlug)
    {
        $this->category = Category::where('slug', $categorySlug)->firstOrFail();

        // ✅ Get all descendant categories
        $this->categoryIds = $this->getDescendantCategories($this->category->id);

        // ✅ Load filters from URL
        $this->selectedOferte = request()->get('oferte') === 'true';
        $this->selectedSize = request()->get('size', null);
        $this->selectedColor = request()->get('color', null);
        $this->selectedMaterial = request()->get('material', null);

        $this->updateAvailableFilters();
    }

    private function getDescendantCategories($categoryId)
    {
        $categories = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        foreach ($categories as $childId) {
            $categories = array_merge($categories, $this->getDescendantCategories($childId));
        }
        return array_merge([$categoryId], $categories);
    }

    private function updateAvailableFilters()
    {
        // ✅ Get all variations from products in the parent + child categories
        $query = ProductVariation::whereHas('product', function ($q) {
            $q->whereIn('category_id', $this->categoryIds);
        });

        if ($this->selectedSize) {
            $query->whereHas('attributeValues', function ($q) {
                $q->where('value', $this->selectedSize);
            });
        }

        if ($this->selectedColor) {
            $query->whereHas('attributeValues', function ($q) {
                $q->where('value', $this->selectedColor);
            });
        }

        if ($this->selectedMaterial) {
            $query->whereHas('attributeValues', function ($q) {
                $q->where('value', $this->selectedMaterial);
            });
        }

        $filteredVariations = $query->get();

        // ✅ Extract and update available filter options dynamically
        $this->availableSizes = $this->extractAvailableAttributes($filteredVariations, 'Mărime');
        $this->availableColors = $this->extractAvailableAttributes($filteredVariations, 'Culoare');
        $this->availableMaterials = $this->extractAvailableAttributes($filteredVariations, 'Material');
    }

    private function extractAvailableAttributes($variations, $attributeName)
    {
        return AttributeValue::whereHas('productVariations', function ($q) use ($variations) {
            $q->whereIn('product_variations.id', $variations->pluck('id'));
        })->whereHas('attribute', function ($q) use ($attributeName) {
            $q->where('attributes.name', $attributeName);
        })->pluck('value')->unique()->values();
    }

    public function updated($property, $value)
    {
        if (in_array($property, ['selectedSize', 'selectedColor', 'selectedMaterial', 'selectedOferte'])) {
            $this->updateAvailableFilters();
            $this->updateUrl();
            $this->emitFilters();
        }
    }

    public function resetFilters()
    {
        $this->selectedOferte = false;
        $this->selectedSize = null;
        $this->selectedColor = null;
        $this->selectedMaterial = null;

        $this->updateAvailableFilters();
        $this->updateUrl();
        $this->emitFilters();
    }

    public function resetOferte()
    {
        $this->selectedOferte = false;
        $this->updateAvailableFilters();
        $this->updateUrl();
        $this->emitFilters();
    }

    private function emitFilters()
    {
        $this->dispatch('filtersUpdated', [
            'selectedOferte' => $this->selectedOferte,
            'selectedSize' => $this->selectedSize,
            'selectedColor' => $this->selectedColor,
            'selectedMaterial' => $this->selectedMaterial
        ]);
    }

    private function updateUrl()
    {
        $queryParams = array_filter([
            'oferte'   => $this->selectedOferte ? 'true' : null,
            'size'     => $this->selectedSize,
            'color'    => $this->selectedColor,
            'material' => $this->selectedMaterial
        ]);

        $baseUrl = url("/produse/{$this->category->slug}");
        $queryString = http_build_query($queryParams);
        $url = $queryString ? "{$baseUrl}?{$queryString}" : $baseUrl;
        $this->dispatch('updateBrowserHistory', ['url' => $url]);
    }

    public function render()
    {
        return view('livewire.product-filters');
    }
}
