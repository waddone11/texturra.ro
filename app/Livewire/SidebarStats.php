<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;

class SidebarStats extends Component
{
    public $categoryCount;
    public $productCount;
    public $productCountEmag;
    public $productCountPrice;
    public $productCountEan;
    //public $paymentCount;

    // Listeners for real-time updates
    protected $listeners = ['refreshSidebarStats' => 'updateCounts'];

    // Update counts when the component is mounted
    public function mount()
    {
        $this->updateCounts();
    }

    // Method to update counts
    public function updateCounts()
    {
        $this->categoryCount = Category::count();
        $this->productCount = Product::count();
        $this->productCountPrice = Product::where('price','0.00')->count();
        $this->productCountEan = Product::whereNull('ean')->count();
        $this->productCountEmag = Product::whereNotNull('ean')->count();
    }

    public function render()
    {
        return view('livewire.sidebar-stats');
    }
}
