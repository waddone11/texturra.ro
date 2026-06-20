<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Faza 4 Grup A.3 safety net.
 *
 * Product currently declares BOTH $guarded = [] AND a 36-column $fillable.
 * Because $fillable is non-empty it already governs mass-assignment (the
 * $guarded = [] line is inert). Faza 4 removes the misleading $guarded line.
 *
 * These tests pin the columns the admin CRUD actually writes via mass-assignment,
 * so if Faza 4 trims $fillable below what the app relies on, they go red —
 * catching the silent "column stops persisting" corruption.
 */
class ProductFillableTest extends TestCase
{
    use RefreshDatabase;

    /** Columns written by App\Livewire\Products\ProductCreate / ProductEdit. */
    private const WRITTEN_COLUMNS = [
        'name', 'description', 'price', 'ean', 'category_id',
        'general_stock', 'product_code', 'status', 'images',
    ];

    private function makeCategory(): Category
    {
        return Category::create(['name' => 'Perdele', 'slug' => 'perdele-' . uniqid()]);
    }

    public function test_admin_written_columns_are_mass_assignable_and_persist(): void
    {
        $cat = $this->makeCategory();

        $product = Product::create([
            'name'          => 'Perdea test',
            'description'   => 'Descriere test',
            'price'         => 199.99,
            'ean'           => 'EAN-' . uniqid(),
            'category_id'   => $cat->id,
            'general_stock' => 5,
            'product_code'  => 'TEX-' . uniqid(),
            'status'        => 1,
        ]);

        $fresh = $product->fresh();

        $this->assertSame('Perdea test', $fresh->name);
        $this->assertSame('Descriere test', $fresh->description);
        $this->assertEquals(199.99, (float) $fresh->price);
        $this->assertSame($cat->id, $fresh->category_id);
        $this->assertSame(5, (int) $fresh->general_stock);
        $this->assertSame(1, (int) $fresh->status);
        $this->assertNotNull($fresh->product_code);
    }

    public function test_fillable_includes_every_column_the_app_writes(): void
    {
        $fillable = (new Product())->getFillable();

        $this->assertNotEmpty($fillable, 'Product must keep an explicit $fillable.');

        foreach (self::WRITTEN_COLUMNS as $col) {
            $this->assertContains(
                $col,
                $fillable,
                "Product \$fillable must include '{$col}' — it is written by the admin CRUD."
            );
        }
    }
}
