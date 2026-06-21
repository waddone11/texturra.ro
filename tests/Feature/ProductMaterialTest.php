<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductMaterialTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_materials_pivot_relation(): void
    {
        $cat = Category::create(['name' => 'Draperii']);
        $product = Product::create([
            'name' => 'Draperie', 'price' => 100, 'category_id' => $cat->id, 'type' => 'custom',
            'status' => 1, 'general_stock' => 0, 'description' => 'x', 'product_code' => 'TEX-M',
        ]);
        $catifea = Material::create(['name' => 'Catifea', 'slug' => 'catifea']);
        $voal = Material::create(['name' => 'Voal', 'slug' => 'voal']);

        $product->materials()->attach([$catifea->id, $voal->id]);

        $this->assertEqualsCanonicalizing(
            [$catifea->id, $voal->id],
            $product->fresh()->materials->pluck('id')->all()
        );
        $this->assertSame(1, $catifea->fresh()->products()->count());
    }
}
