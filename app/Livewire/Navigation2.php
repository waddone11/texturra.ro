<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;

class Navigation2 extends Component
{
    public $topCategories;
    public $activeCategoryId = null;
    public $subcategories = [];
    public $selectedSubcategoryId = null;
    public $products = [];

    public function mount()
    {
        // Fetch top-level categories (Barbati, Femei, Copii)
        $this->topCategories = Category::whereNull('parent_id')->where('status', 1)->get();

        if ($this->topCategories->isNotEmpty()) {
            $this->activeCategoryId = $this->topCategories->first()->id;
            $this->loadSubcategories();
        }
    }

    public function setActiveCategory($categoryId)
    {
        $this->activeCategoryId = $categoryId;
        $this->loadSubcategories();
        $this->selectedSubcategoryId = null;  // Reset the selected subcategory
        $this->products = [];  // Reset the displayed products
    }

    public function setSubcategory($subcategoryId)
    {
        $this->selectedSubcategoryId = $subcategoryId;
        $this->loadProducts();
    }

    public function loadSubcategories()
    {
        $this->subcategories = Category::
            where('parent_id', $this->activeCategoryId)
            ->where('status', 1)
            ->get();
    }

    public function loadProducts()
    {
        // Fetch products based on the selected subcategory
        $this->products = Product::where('category_id', $this->selectedSubcategoryId)->take(3)->get();
    }

    public function render()
    {
        return view('livewire.navigation', [
            'topCategories' => $this->topCategories,
            'subcategories' => $this->subcategories,
            'products' => $this->products,
        ]);
    }
}


