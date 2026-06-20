<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
//use App\Models\Payment;

class SidebarAccount extends Component
{
    public $categoryCount;
    public $productCount;
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
//        $this->categoryCount = Category::count();
//        $this->productCount = Product::count();
    }

    public function render()
    {
        return view('livewire.sidebar-account');
    }
}
