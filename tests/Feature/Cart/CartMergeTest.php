<?php

namespace Tests\Feature\Cart;

use App\Enums\UserType;
use App\Livewire\Pages\Auth\Login;
use App\Models\Cart;
use App\Models\Category;
use App\Models\ManufactoringType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Session->user cart merge — identity key = product_id + length + height +
 * manufactoring_type_id + pieces. Same key => sum quantity; different => separate
 * rows; all custom curtain fields preserved. Drives the real login() money path.
 */
class CartMergeTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create(['type' => UserType::CLIENT, 'password' => Hash::make('password')]);
    }

    private function makeProduct(): Product
    {
        $cat = Category::create(['name' => 'Perdele', 'slug' => 'perdele-' . uniqid()]);

        return Product::create([
            'name' => 'Produs ' . uniqid(), 'description' => 'desc', 'price' => 99.99,
            'ean' => 'EAN-' . uniqid(), 'category_id' => $cat->id, 'general_stock' => 5,
            'product_code' => 'TEX-' . uniqid(), 'status' => 1,
        ]);
    }

    private function makeType(): ManufactoringType
    {
        return ManufactoringType::create(['name' => 'Manopera ' . uniqid(), 'price' => 15.00]);
    }

    private function loginWith(User $user): void
    {
        session(['eligible_to_transfer_cart' => true]);

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasNoErrors();
    }

    /** (a) session-only custom item: row created, every custom field intact. */
    public function test_custom_item_dimensions_are_preserved(): void
    {
        $user = $this->makeUser();
        $p = $this->makeProduct();
        $t = $this->makeType();
        $sid = session()->getId();

        Cart::create([
            'session_id' => $sid, 'product_id' => $p->id,
            'length' => 2.5, 'height' => 1.8, 'manufactoring_type_id' => $t->id,
            'pieces' => 2, 'quantity' => 1, 'price' => 120.00,
        ]);

        $this->loginWith($user);

        $rows = Cart::where('user_id', $user->id)->get();
        $this->assertCount(1, $rows);
        $r = $rows->first();
        $this->assertEqualsWithDelta(2.5, (float) $r->length, 0.001);
        $this->assertEqualsWithDelta(1.8, (float) $r->height, 0.001);
        $this->assertEquals($t->id, $r->manufactoring_type_id);
        $this->assertEquals(2, (int) $r->pieces);
        $this->assertEqualsWithDelta(120.00, (float) $r->price, 0.001);
    }

    /** (b) identical key in both carts: one row, quantities summed. */
    public function test_identical_key_sums_quantities(): void
    {
        $user = $this->makeUser();
        $p = $this->makeProduct();
        $t = $this->makeType();
        $sid = session()->getId();

        Cart::create([
            'user_id' => $user->id, 'product_id' => $p->id,
            'length' => 2.5, 'height' => 1.8, 'manufactoring_type_id' => $t->id,
            'pieces' => 2, 'quantity' => 1, 'price' => 120.00,
        ]);
        Cart::create([
            'session_id' => $sid, 'product_id' => $p->id,
            'length' => 2.5, 'height' => 1.8, 'manufactoring_type_id' => $t->id,
            'pieces' => 2, 'quantity' => 2, 'price' => 120.00,
        ]);

        $this->loginWith($user);

        $rows = Cart::where('user_id', $user->id)->get();
        $this->assertCount(1, $rows);
        $this->assertEquals(3, (int) $rows->first()->quantity); // 1 + 2
    }

    /** (c) same product, different dimensions: two separate rows. */
    public function test_different_dimensions_stay_separate(): void
    {
        $user = $this->makeUser();
        $p = $this->makeProduct();
        $t = $this->makeType();
        $sid = session()->getId();

        Cart::create([
            'user_id' => $user->id, 'product_id' => $p->id,
            'length' => 2.0, 'height' => 1.5, 'manufactoring_type_id' => $t->id,
            'pieces' => 2, 'quantity' => 1, 'price' => 100.00,
        ]);
        Cart::create([
            'session_id' => $sid, 'product_id' => $p->id,
            'length' => 2.5, 'height' => 1.8, 'manufactoring_type_id' => $t->id,
            'pieces' => 2, 'quantity' => 1, 'price' => 120.00,
        ]);

        $this->loginWith($user);

        $this->assertEquals(2, Cart::where('user_id', $user->id)->count());
    }

    /** (d) same product+dimensions, different pieces (identity): two separate rows. */
    public function test_different_pieces_stay_separate(): void
    {
        $user = $this->makeUser();
        $p = $this->makeProduct();
        $t = $this->makeType();
        $sid = session()->getId();

        Cart::create([
            'user_id' => $user->id, 'product_id' => $p->id,
            'length' => 2.5, 'height' => 1.8, 'manufactoring_type_id' => $t->id,
            'pieces' => 1, 'quantity' => 1, 'price' => 60.00,
        ]);
        Cart::create([
            'session_id' => $sid, 'product_id' => $p->id,
            'length' => 2.5, 'height' => 1.8, 'manufactoring_type_id' => $t->id,
            'pieces' => 2, 'quantity' => 1, 'price' => 120.00,
        ]);

        $this->loginWith($user);

        $this->assertEquals(2, Cart::where('user_id', $user->id)->count());
    }
}
