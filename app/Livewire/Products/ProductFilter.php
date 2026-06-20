<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\Category;

class ProductFilter extends Component
{
    public $searchName = '';
    public $searchId = '';
    public $selectedCategory = null;
    public $status = null;      // Active (1) or Archived (0)
    public $eanFilter = null;   // "1" = With EAN, "0" = Without EAN
    public $priceFilter = null; // "1" = With Price (> 0), "0" = Without Price (0 or null)

    // We don't pass these in the URL
    protected $queryString = [];

    public function applyFilters()
    {
        $this->dispatch('applyFilters', [
            'searchName'       => $this->searchName,
            'searchId'         => $this->searchId,
            'selectedCategory' => $this->selectedCategory,
            'status'           => $this->status,
            'eanFilter'        => $this->eanFilter,
            'priceFilter'      => $this->priceFilter,
        ]);
    }

    public function resetFilters()
    {
        $this->searchName = '';
        $this->searchId = '';
        $this->selectedCategory = null;
        $this->status = null;
        $this->eanFilter = null;
        $this->priceFilter = null;

        $this->dispatch('applyFilters', [
            'searchName'       => '',
            'searchId'         => '',
            'selectedCategory' => null,
            'status'           => null,
            'eanFilter'        => null,
            'priceFilter'      => null,
        ]);
    }

    public function render()
    {
        return view('livewire.products.product-filter', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
