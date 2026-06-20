<?php

namespace Tests\Feature\Cart;

use App\Enums\UserType;
use App\Models\Cart;
use App\Models\Category;
use App\Models\ManufactoringType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * addStandardProduct must only ever touch STANDARD rows (no dimensions). It must
 * never match/clobber a CUSTOM row of the same product. Identity for a standard
 * line = product_id + null dimensions.
 */
class CartAddStandardTest extends TestCase
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
            'ean' => 'EAN-' . uniqid(), 'category_id' => $cat->id, 'general_stock' => 5,
            'product_code' => 'TEX-' . uniqid(), 'status' => 1,
        ]);
    }

    private function makeType(): ManufactoringType
    {
        return ManufactoringType::create(['name' => 'Manopera ' . uniqid(), 'price' => 15.00]);
    }

    /** (a) Standard add must NOT clobber an existing custom row of the same product. */
    public function test_standard_add_does_not_clobber_custom_row(): void
    {
        $user = $this->makeUser();
        $p = $this->makeProduct();
        $t = $this->makeType();
        $this->actingAs($user);

        $custom = Cart::create([
            'user_id' => $user->id, 'product_id' => $p->id,
            'length' => 2.5, 'height' => 1.8, 'manufactoring_type_id' => $t->id,
            'pieces' => 2, 'quantity' => 1, 'price' => 120.00,
        ]);

        $this->post(route('cart.add.standard'), ['product_id' => $p->id, 'quantity' => 1]);

        // Custom row must be intact (dimensions + price preserved).
        $custom->refresh();
        $this->assertEqualsWithDelta(2.5, (float) $custom->length, 0.001);
        $this->assertEqualsWithDelta(1.8, (float) $custom->height, 0.001);
        $this->assertEquals($t->id, $custom->manufactoring_type_id);
        $this->assertEquals(2, (int) $custom->pieces);
        $this->assertEqualsWithDelta(120.00, (float) $custom->price, 0.001);

        // Standard add lands as a SEPARATE row with null dimensions.
        $this->assertEquals(2, Cart::where('user_id', $user->id)->count());
        $standard = Cart::where('user_id', $user->id)->whereNull('length')->first();
        $this->assertNotNull($standard);
        $this->assertNull($standard->height);
    }

    /** (b) Re-adding the same standard product sums quantity on the one standard row. */
    public function test_standard_re_add_sums_quantity(): void
    {
        $user = $this->makeUser();
        $p = $this->makeProduct();
        $this->actingAs($user);

        Cart::create([
            'user_id' => $user->id, 'product_id' => $p->id,
            'length' => null, 'height' => null, 'manufactoring_type_id' => null,
            'pieces' => 1, 'quantity' => 1, 'price' => 99.99,
        ]);

        $this->post(route('cart.add.standard'), ['product_id' => $p->id, 'quantity' => 2]);

        $this->assertEquals(1, Cart::where('user_id', $user->id)->count());
        $this->assertEquals(3, (int) Cart::where('user_id', $user->id)->first()->quantity);
    }

    /** (c) Empty cart: standard add creates a clean standard row. */
    public function test_standard_add_to_empty_cart(): void
    {
        $user = $this->makeUser();
        $p = $this->makeProduct();
        $this->actingAs($user);

        $this->post(route('cart.add.standard'), ['product_id' => $p->id, 'quantity' => 1]);

        $this->assertEquals(1, Cart::where('user_id', $user->id)->count());
        $row = Cart::where('user_id', $user->id)->first();
        $this->assertNull($row->length);
        $this->assertEquals(1, (int) $row->quantity);
    }

    /** (d) Regression: a standard row and a custom row of the same product coexist. */
    public function test_standard_and_custom_rows_coexist(): void
    {
        $user = $this->makeUser();
        $p = $this->makeProduct();
        $t = $this->makeType();
        $this->actingAs($user);

        // standard already in cart
        Cart::create([
            'user_id' => $user->id, 'product_id' => $p->id,
            'pieces' => 1, 'quantity' => 1, 'price' => 99.99,
        ]);

        // add same product as custom via the custom endpoint
        $this->post(route('cart.add.custom'), [
            'product_id' => $p->id, 'length' => 2.5, 'height' => 1.8,
            'manufactoring_type_id' => $t->id, 'pieces' => 2,
        ]);

        $this->assertEquals(2, Cart::where('user_id', $user->id)->count());
    }
}
