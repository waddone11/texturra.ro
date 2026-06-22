<?php

namespace Tests\Feature\Cart;

use App\Enums\UserType;
use App\Livewire\CartWidgetMenu;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The nav cart badge (CartWidgetMenu) must count CUSTOM lines too. Custom curtains
 * store NULL quantity but carry `pieces`, so a custom-only cart must not show "0".
 * Counting only — price/cart-line logic is not exercised here.
 */
class CartBadgeTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create(['type' => UserType::CLIENT]);
    }

    private function makeProduct(): Product
    {
        $cat = Category::create(['name' => 'Perdele', 'slug' => 'perdele-' . uniqid()]);

        return Product::create([
            'name' => 'Produs ' . uniqid(), 'description' => 'desc', 'price' => 99.99,
            'ean' => 'EAN-' . uniqid(), 'category_id' => $cat->id, 'general_stock' => 50,
            'product_code' => 'TEX-' . uniqid(), 'status' => 1,
        ]);
    }

    /** A custom-only cart (quantity NULL, pieces set) must NOT render badge 0. */
    public function test_badge_counts_custom_product_pieces(): void
    {
        $user = $this->makeUser();
        $product = $this->makeProduct();

        Cart::create([
            'user_id' => $user->id, 'product_id' => $product->id,
            'length' => 2.0, 'height' => 2.8, 'pieces' => 2, 'price' => 120.00,
        ]);

        $this->actingAs($user);

        Livewire::test(CartWidgetMenu::class)->assertSet('cartCount', 2);
    }

    /** Mixed cart: standard quantity + custom pieces are summed. */
    public function test_badge_counts_mixed_cart(): void
    {
        $user = $this->makeUser();
        $product = $this->makeProduct();

        Cart::create([ // standard line, quantity 3
            'user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 3, 'price' => 10.00,
        ]);
        Cart::create([ // custom line, pieces 2 (quantity NULL)
            'user_id' => $user->id, 'product_id' => $product->id,
            'length' => 2.0, 'pieces' => 2, 'price' => 50.00,
        ]);

        $this->actingAs($user);

        Livewire::test(CartWidgetMenu::class)->assertSet('cartCount', 5); // 3 + 2
    }
}
