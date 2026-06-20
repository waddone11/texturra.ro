<?php

namespace Tests\Feature\Auth;

use App\Enums\UserType;
use App\Livewire\Pages\Auth\Login;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A2b witness — login flow including the session->user cart merge (money path).
 * Pins current behaviour so the routing reconciliation can be proven neutral.
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(): Product
    {
        $cat = Category::create(['name' => 'Perdele', 'slug' => 'perdele-' . uniqid()]);

        return Product::create([
            'name'          => 'Produs ' . uniqid(),
            'description'   => 'desc',
            'price'         => 99.99,
            'ean'           => 'EAN-' . uniqid(),
            'category_id'   => $cat->id,
            'general_stock' => 5,
            'product_code'  => 'TEX-' . uniqid(),
            'status'        => 1,
        ]);
    }

    public function test_route_resolves_to_login_class(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSeeLivewire(Login::class);
    }

    public function test_user_can_authenticate(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT, 'password' => Hash::make('password')]);

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_show_error(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT, 'password' => Hash::make('password')]);

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('form.email');

        $this->assertGuest();
    }

    public function test_session_cart_merges_into_user_on_login(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT, 'password' => Hash::make('password')]);
        $productA = $this->makeProduct();
        $productB = $this->makeProduct();

        $sessionId = session()->getId();

        // Guest session cart, with custom curtain dimensions on item A.
        Cart::create([
            'session_id' => $sessionId, 'product_id' => $productA->id,
            'quantity' => 2, 'price' => 50.00, 'length' => 1.50, 'height' => 2.00, 'pieces' => 3,
        ]);
        Cart::create([
            'session_id' => $sessionId, 'product_id' => $productB->id,
            'quantity' => 1, 'price' => 30.00,
        ]);

        session(['eligible_to_transfer_cart' => true]);

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('checkout.index'));

        // Both products land in the user's cart with quantity + price.
        $this->assertDatabaseHas('carts', ['user_id' => $user->id, 'product_id' => $productA->id, 'quantity' => 2, 'price' => 50.00]);
        $this->assertDatabaseHas('carts', ['user_id' => $user->id, 'product_id' => $productB->id, 'quantity' => 1, 'price' => 30.00]);

        // Session cart items consumed (no duplicates / leftovers).
        $this->assertDatabaseMissing('carts', ['session_id' => $sessionId]);

        // KNOWN CURRENT BEHAVIOUR (documented, not endorsed): the merge copies only
        // quantity + price, so custom dimensions (length/height/pieces) are LOST.
        $merged = Cart::where('user_id', $user->id)->where('product_id', $productA->id)->first();
        $this->assertNull($merged->length, 'merge currently drops length (data-loss flagged for separate fix)');
        $this->assertNull($merged->height);
    }

    public function test_login_without_session_cart_does_not_break(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT, 'password' => Hash::make('password')]);

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertDatabaseCount('carts', 0);
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        $user = User::factory()->create(['type' => UserType::CLIENT, 'password' => Hash::make('password')]);

        foreach (range(1, 5) as $i) {
            Livewire::test(Login::class)
                ->set('form.email', $user->email)
                ->set('form.password', 'wrong-password')
                ->call('login');
        }

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('form.email');

        $this->assertGuest();
    }
}
