<?php

namespace App\Livewire\Account;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class MyFavorites extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function render()
    {
        $favorites = Favorite::where('user_id', Auth::id())
            ->whereHas('product', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.account.my-favorites', compact('favorites'))
            ->extends('layouts.base')
            ->section('content');
    }

    public function removeFavorite($productId)
    {
        Favorite::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();

        $this->emit('refreshFavoritesCount');
    }
}
