<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Log; // Import the Log facade
use DB;

class SearchModal extends Component
{
    public $searchQuery = '';
    public $results = [];
    public $modalOpen = false;


    public function updatedSearchQuery()
    {
        \Log::info('Search Query Updated:', ['query' => $this->searchQuery]);

        if (!empty($this->searchQuery)) {
            DB::enableQueryLog(); // Enable query log

            $this->results = Product::where('name', 'like', '%' . $this->searchQuery . '%')
                ->orWhere('description_plain', 'like', '%' . $this->searchQuery . '%')
                ->limit(10)
                ->get();

            \Log::info('Executed Query:', DB::getQueryLog()); // Log executed query
        } else {
            $this->results = [];
        }
    }


    public function getImageUrlAttribute()
    {
        $images = $this->images ? json_decode($this->images, true) : [];
        return $images[0] ?? 'path/to/default-image.jpg';
    }


    public function render()
    {
        \Log::info('Render State:', [
            'searchQuery' => $this->searchQuery,
            'results' => $this->results,
            'modalOpen' => $this->modalOpen,
        ]);
        return view('livewire.search-modal');
    }
}
