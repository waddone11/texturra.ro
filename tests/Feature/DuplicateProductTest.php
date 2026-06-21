<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Category;
use App\Models\Color;
use App\Models\ColorGroup;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuplicateProductTest extends TestCase
{
    use RefreshDatabase;

    private function source(): Product
    {
        $cat = Category::create(['name' => 'Covoare']);
        $product = Product::create([
            'name' => 'Covor Model X 2x3', 'price' => 200, 'category_id' => $cat->id,
            'type' => 'standard', 'status' => 1, 'general_stock' => 9, 'description' => 'd',
            'ean' => '111', 'product_code' => 'TEX-SOURCE',
        ]);
        $group = ColorGroup::create(['name' => 'Roșu', 'image_path' => 'x']);
        $color = Color::create(['color_group_id' => $group->id, 'name' => 'Roșu', 'cod_css' => '#FF0000']);
        $product->colors()->attach($color->id, ['stock' => 5]);

        return $product;
    }

    public function test_duplicate_creates_sibling_sharing_group_with_fresh_unique_fields(): void
    {
        $source = $this->source();

        $copy = ProductsTable::duplicateToSize($source, ['name' => 'Covor Model X 3x4', 'height' => 3.0])->refresh();

        // source now has a group; copy shares it -> they are siblings
        $source->refresh();
        $this->assertNotNull($source->model_group_id);
        $this->assertSame($source->model_group_id, $copy->model_group_id);
        $this->assertTrue($source->hasSiblings());
        $this->assertTrue($copy->hasSiblings());

        // unique fields regenerated / cleared
        $this->assertNotSame($source->product_code, $copy->product_code);
        $this->assertStringStartsWith('TEX-', $copy->product_code);
        $this->assertNull($copy->ean);
        $this->assertNull($copy->emag_id);

        // copy data
        $this->assertSame('Covor Model X 3x4', $copy->name);
        $this->assertEqualsWithDelta(3.0, (float) $copy->height, 0.001);
        $this->assertSame(0, (int) $copy->general_stock);
        $this->assertNotSame($source->slug, $copy->slug);
    }

    public function test_duplicate_inherits_palette_with_zero_stock(): void
    {
        $source = $this->source();

        $copy = ProductsTable::duplicateToSize($source, ['name' => 'Covor Model X 1x2']);

        $this->assertSame(1, $copy->colors()->count());
        $this->assertSame(0, (int) $copy->colors()->first()->pivot->stock); // source had 5, copy starts 0
    }

    public function test_duplicate_of_grouped_product_joins_same_group(): void
    {
        $source = $this->source();
        $first = ProductsTable::duplicateToSize($source, ['name' => 'A']);
        $second = ProductsTable::duplicateToSize($source->refresh(), ['name' => 'B']);

        $this->assertSame($source->fresh()->model_group_id, $first->model_group_id);
        $this->assertSame($source->fresh()->model_group_id, $second->model_group_id);
        // source sees 2 siblings
        $this->assertCount(2, $source->fresh()->siblings()->get());
    }
}
