<?php

namespace Tests\Feature\Models;

use App\Enums\UserType;
use App\Models\Address;
use App\Models\AwbLog;
use App\Models\InvoiceAddress;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Faza 4 Grup A.2 regression.
 *
 * SKIPPED until the model fix: AwbLog::$fillable lists 'awb_number' and
 * 'courier_name', neither of which exist on the awb_logs table. The real
 * columns (confirmed in Faza 1) are order_id, response, courier_type, status.
 * (awb_number actually lives on the orders table.) Faza 4 corrects $fillable
 * to the real columns and removes this skip.
 */
class AwbLogFillableTest extends TestCase
{
    use RefreshDatabase;

    public function test_awb_log_real_columns_are_mass_assignable(): void
    {
        $this->markTestSkipped('Pending Faza 4 Grup A.2: AwbLog $fillable lists awb_number/courier_name (non-existent); real column is courier_type.');

        $user = User::factory()->create(['type' => UserType::CLIENT]);

        $addr = Address::create([
            'user_id' => $user->id, 'name' => 'Acasa', 'street' => 'Str. Test 1',
            'city' => 'Bucuresti', 'state' => 'B', 'postal_code' => '010101',
        ]);
        $inv = InvoiceAddress::create([
            'user_id' => $user->id, 'name' => 'Firma', 'street' => 'Str. Test 1',
            'city' => 'Bucuresti', 'state' => 'B', 'postal_code' => '010101',
        ]);

        $order = Order::create([
            'user_id'             => $user->id,
            'order_number'        => 'ORD-' . uniqid(),
            'total_amount'        => 0,
            'shipping_address_id' => $addr->id,
            'billing_address_id'  => $inv->id,
        ]);

        $awb = AwbLog::create([
            'order_id'     => $order->id,
            'courier_type' => 'dpd',
            'status'       => 'pending',
            'response'     => '{}',
        ]);

        $fresh = $awb->fresh();
        $this->assertSame('dpd', $fresh->courier_type);
        $this->assertSame('pending', $fresh->status);
    }
}
