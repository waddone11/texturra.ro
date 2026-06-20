<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class ProductList extends Component
{
    use WithPagination;

    public $search = '';
    public $searchById = '';
    public $selectedCategoryId = null;
    public $status = '1'; // Default: Active products only
    public $eanFilter = null; // "1" = With EAN, "0" = Without EAN, NULL = All
    public $priceFilter = null; // "1" = With Price (> 0), "0" = Without Price (0 or null), NULL = All

    protected $queryString = [
        'search'             => ['except' => ''],
        'searchById'         => ['except' => ''],
        'selectedCategoryId' => ['except' => null],
        'status'             => ['except' => null],
        'eanFilter'          => ['except' => null],
        'priceFilter'        => ['except' => null],
    ];

    protected $listeners = [
        'applyFilters'   => 'updateFilters', // Listen for the event from ProductFilter
        'productUpdated' => '$refresh',
        'productDeleted' => '$refresh',
        'productRestored'=> '$refresh'
    ];

    public function updateFilters($filters)
    {
        $this->search = $filters['searchName'];
        $this->searchById = $filters['searchId'];
        $this->selectedCategoryId = $filters['selectedCategory'];
        $this->status = $filters['status'];
        $this->eanFilter = $filters['eanFilter'];
        $this->priceFilter = $filters['priceFilter'];
        $this->resetPage();
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        if ($product) {
            $product->update(['status' => 0]);
            $this->dispatch('flashMessage', ['type' => 'success', 'message' => 'Produsul a fost arhivat cu succes!']);
            $this->dispatch('productDeleted');
        } else {
            \Log::error('Product not found:', ['id' => $id]);
        }
    }

    public function restoreProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 1]);
        $this->dispatch('flashMessage', ['type' => 'success', 'message' => 'Produsul a fost reactivat!']);
        $this->dispatch('productRestored');
    }

    public function render()
    {
        $query = Product::with('category');
            //->whereNull('ean');

        if ($this->status !== null) {
            $query->where('status', $this->status);
        }
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        if (!empty($this->searchById)) {
            $query->where('id', '=', intval($this->searchById));
        }
        if (!empty($this->selectedCategoryId)) {
            $query->where('category_id', $this->selectedCategoryId);
        }
        if ($this->eanFilter === "1") {
            $query->whereNotNull('ean');
        } elseif ($this->eanFilter === "0") {
            $query->whereNull('ean');
        }
        if ($this->priceFilter === "1") {
            $query->where('price', '>', 0);
        } elseif ($this->priceFilter === "0") {
            $query->where(function($q) {
                $q->whereNull('price')
                    ->orWhere('price', 0);
            });
        }

        $products = $query->orderBy('id','desc')->paginate(10);

        return view('livewire.products.product-list', [
            'products'   => $products,
            'categories' => Category::with('children')->whereNull('parent_id')->get(),
        ])->extends('layouts.base')->section('content');
    }
}
