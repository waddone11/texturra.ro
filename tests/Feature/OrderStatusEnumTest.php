<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class OrderStatusEnumTest extends TestCase
{
    use RefreshDatabase;

    /** The enum covers exactly the values the MySQL orders.status enum allows. */
    public function test_enum_covers_db_status_values(): void
    {
        $values = array_map(fn (OrderStatus $c) => $c->value, OrderStatus::cases());

        $this->assertSame(
            ['pending', 'placed', 'processing', 'completed', 'canceled'],
            $values,
        );
    }

    /** Every real/legacy status in the data must resolve — "placed" is what all 6 orders have. */
    public function test_existing_placed_status_resolves(): void
    {
        $this->assertSame(OrderStatus::Placed, OrderStatus::tryFrom('placed'));
    }

    /** The status column cast turns the stored string into the enum (no save needed). */
    public function test_status_attribute_is_cast_to_enum(): void
    {
        $order = new Order(['status' => 'placed']);

        $this->assertInstanceOf(OrderStatus::class, $order->status);
        $this->assertSame(OrderStatus::Placed, $order->status);
        $this->assertSame('Plasată', $order->status->getLabel());
    }

    /** isRefundable still works after the cast (was a raw string compare). */
    public function test_is_refundable_only_when_completed(): void
    {
        $this->assertTrue((new Order(['status' => 'completed']))->isRefundable());
        $this->assertFalse((new Order(['status' => 'placed']))->isRefundable());
    }

    /** Every case exposes a RO label and a Filament colour. */
    public function test_all_cases_have_label_and_color(): void
    {
        foreach (OrderStatus::cases() as $case) {
            $this->assertNotEmpty($case->getLabel());
            $this->assertNotEmpty($case->getColor());
        }
    }

    /**
     * The client "my-orders" page renders the enum-cast status without fatal
     * (it used ucfirst() on the raw string — now ucfirst($order->status?->value)).
     * Display stays identical: "Placed" (ucfirst of the backing value).
     */
    public function test_my_orders_page_renders_status_after_cast(): void
    {
        $user = User::factory()->create();
        $this->makeOrder($user);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Account\MyOrders::class)
            ->assertOk()
            ->assertSee('Placed')          // ucfirst('placed') — display unchanged
            ->assertSee('OF-TEST-0001');
    }

    /**
     * The Filament admin order pages render the enum status: the table badge and
     * the edit Select both show the RO label ("Plasată") from HasLabel — proving
     * the enum-class options and the badge column work with the cast.
     */
    public function test_filament_admin_order_pages_render_enum_status(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $order = $this->makeOrder($admin);

        $this->actingAs($admin);

        $this->get('/admin/orders')->assertOk()->assertSee('Plasată');
        $this->get("/admin/orders/{$order->id}/edit")->assertOk()->assertSee('Plasată');
    }

    private function makeOrder(User $user): Order
    {
        $addr = DB::table('addresses')->insertGetId([
            'user_id' => $user->id, 'name' => 'X', 'street' => 'S', 'city' => 'C',
            'state' => 'St', 'postal_code' => '000', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $inv = DB::table('invoice_addresses')->insertGetId([
            'user_id' => $user->id, 'name' => 'X', 'street' => 'S', 'city' => 'C',
            'state' => 'St', 'postal_code' => '000', 'created_at' => now(), 'updated_at' => now(),
        ]);

        return Order::create([
            'user_id' => $user->id, 'order_number' => 'OF-TEST-0001', 'status' => 'placed',
            'total_amount' => 100, 'subtotal_excluding_vat' => 80, 'total_vat' => 20,
            'discount' => 0, 'shipping_cost' => 0,
            'shipping_address_id' => $addr, 'billing_address_id' => $inv,
        ]);
    }
}
