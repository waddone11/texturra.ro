<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\RelationManagers\SiblingsRelationManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class SiblingCategoryWarningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['type' => 'admin']));
    }

    private function product(string $name, Category $cat): Product
    {
        return Product::create([
            'name' => $name, 'price' => 10, 'category_id' => $cat->id,
            'type' => 'standard', 'status' => 1, 'general_stock' => 0,
            'description' => 'x', 'product_code' => 'TST-' . Str::slug($name),
        ]);
    }

    private function attach(Product $owner, Product $sibling): \Livewire\Features\SupportTesting\Testable
    {
        return Livewire::test(SiblingsRelationManager::class, [
            'ownerRecord' => $owner,
            'pageClass' => EditProduct::class,
        ])->callAction(
            TestAction::make('attachExisting')->table(),
            ['product_id' => $sibling->id],
        );
    }

    /** Different categories → WARN, but the sibling is still linked (never blocked). */
    public function test_cross_category_attach_warns_but_still_links(): void
    {
        $owner = $this->product('Covor 2x3', Category::create(['name' => 'Covoare']));
        $sibling = $this->product('Draperie', Category::create(['name' => 'Draperii']));

        $this->attach($owner, $sibling)->assertNotified();

        // The link went through despite the warning — guard is advisory, not a block.
        $this->assertNotNull($owner->fresh()->model_group_id);
        $this->assertSame($owner->fresh()->model_group_id, $sibling->fresh()->model_group_id);
    }

    /** Same category → no warning, links normally. */
    public function test_same_category_attach_has_no_warning(): void
    {
        $cat = Category::create(['name' => 'Covoare']);
        $owner = $this->product('Covor 2x3', $cat);
        $sibling = $this->product('Covor 3x4', $cat);

        $this->attach($owner, $sibling)->assertNotNotified();

        $this->assertSame($owner->fresh()->model_group_id, $sibling->fresh()->model_group_id);
        $this->assertNotNull($owner->fresh()->model_group_id);
    }
}
