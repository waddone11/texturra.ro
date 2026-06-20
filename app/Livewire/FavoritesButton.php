<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoritesButton extends Component
{
    public $productId;
    public $isFavorite = false;
    public string $layoutKey = '';

    public function mount($productId, $layoutKey = '')
    {
        $this->productId = $productId;
        $this->layoutKey = $layoutKey;
        $this->isFavorite = Auth::check() ? Favorite::where('user_id', Auth::id())->where('product_id', $this->productId)->exists() : false;
    }

    public function toggleFavorite()
    {
        if (!Auth::check()) {
            $this->dispatch('showFavoriteLoginModal');
            return;
        }

        if ($this->isFavorite) {
            Favorite::where('user_id', Auth::id())->where('product_id', $this->productId)->delete();
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'product_id' => $this->productId
            ]);
        }

        $this->isFavorite = !$this->isFavorite;
        $this->dispatch('refreshFavoritesCount');
    }

    public function render()
    {
        return view('livewire.favorites-button');
    }
}

