<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiblingProductsTest extends TestCase
{
    use RefreshDatabase;

    private Category $cat;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cat = Category::create(['name' => 'Covoare']);
    }

    private function product(string $name, ?string $group, float $price = 10): Product
    {
        return Product::create([
            'name' => $name, 'price' => $price, 'category_id' => $this->cat->id,
            'type' => 'standard', 'status' => 1, 'general_stock' => 0,
            'description' => 'x', 'model_group_id' => $group,
            'product_code' => 'TST-' . \Illuminate\Support\Str::slug($name), // unique (DB has unique index)
        ]);
    }

    public function test_siblings_share_model_group_excluding_self(): void
    {
        $a = $this->product('Covor 2x3', 'mdl-1', 200);
        $b = $this->product('Covor 3x4', 'mdl-1', 300);
        $c = $this->product('Covor 1x2', 'mdl-1', 100);

        $this->assertEqualsCanonicalizing([$b->id, $c->id], $a->siblings()->pluck('id')->all());
        $this->assertCount(2, $a->siblings()->get());
        $this->assertTrue($a->hasSiblings());

        // ordered by price ascending (c=100, b=300)
        $this->assertSame([$c->id, $b->id], $a->siblings()->pluck('id')->all());
    }

    public function test_ungrouped_product_has_no_siblings(): void
    {
        $solo = $this->product('Singur', null);
        $this->product('Altul', 'mdl-1');

        $this->assertCount(0, $solo->siblings()->get());
        $this->assertFalse($solo->hasSiblings());
    }

    public function test_products_in_different_groups_are_not_siblings(): void
    {
        $a = $this->product('A', 'mdl-1');
        $this->product('X', 'mdl-2');

        $this->assertCount(0, $a->siblings()->get());
        $this->assertFalse($a->hasSiblings());
    }
}
