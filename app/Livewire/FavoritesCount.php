<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoritesCount extends Component
{
    public $count = 0;

    protected $listeners = ['refreshFavoritesCount' => 'loadFavoritesCount'];

    public function mount()
    {
        $this->loadFavoritesCount();
    }

    public function loadFavoritesCount()
    {
        $this->count = Auth::check() ? Favorite::where('user_id', Auth::id())->count() : 0;
    }

    public function render()
    {
        return view('livewire.favorites-count');
    }
}
