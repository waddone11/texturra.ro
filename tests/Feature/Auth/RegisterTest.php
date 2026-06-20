<?php

namespace Tests\Feature\Auth;

use App\Livewire\Pages\Auth\Register;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * A2b witness — register flow including verification + session->user cart merge.
 */
class RegisterTest extends TestCase
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

    public function test_route_resolves_to_register_class(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSeeLivewire(Register::class);
    }

    public function test_new_user_is_created_and_logged_in(): void
    {
        Notification::fake();

        Livewire::test(Register::class)
            ->set('form.name', 'New Client')
            ->set('form.email', 'newclient@example.com')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('home'));

        $user = User::where('email', 'newclient@example.com')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, CustomVerifyEmail::class);
    }

    public function test_password_must_be_confirmed(): void
    {
        Livewire::test(Register::class)
            ->set('form.name', 'New Client')
            ->set('form.email', 'newclient@example.com')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'mismatch')
            ->call('register')
            ->assertHasErrors('form.password');

        $this->assertGuest();
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        Livewire::test(Register::class)
            ->set('form.name', 'New Client')
            ->set('form.email', 'taken@example.com')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors('form.email');
    }

    public function test_session_cart_merges_into_new_user_on_register(): void
    {
        Notification::fake();
        $product = $this->makeProduct();

        $sessionId = session()->getId();
        Cart::create([
            'session_id' => $sessionId, 'product_id' => $product->id,
            'quantity' => 4, 'price' => 25.00,
        ]);
        session(['eligible_to_transfer_cart' => true]);

        Livewire::test(Register::class)
            ->set('form.name', 'Cart Client')
            ->set('form.email', 'cartclient@example.com')
            ->set('form.password', 'password123')
            ->set('form.password_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('checkout.index'));

        $user = User::where('email', 'cartclient@example.com')->first();
        $this->assertDatabaseHas('carts', ['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 4]);
        $this->assertDatabaseMissing('carts', ['session_id' => $sessionId]);
    }
}
