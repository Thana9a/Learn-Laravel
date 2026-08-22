<?php

namespace Tests\Feature;

use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderDiscount;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_order_successfully_with_discount_and_stock_decrement(): void
    {
        $user = User::factory()->create();

        $espresso = Product::create([
            'name'     => 'Signature Espresso',
            'category' => 'Coffee',
            'price'    => 4.00,
            'stock'    => 20,
        ]);

        $croissant = Product::create([
            'name'     => 'Butter Croissant',
            'category' => 'Pastries',
            'price'    => 3.00,
            'stock'    => 15,
        ]);

        $discount = Discount::create([
            'name'        => '10% Off Grand Opening',
            'code'        => 'WELCOME10',
            'type'        => 'percentage',
            'value'       => 10.00,
            'usage_limit' => 50,
            'used_count'  => 0,
            'start_date'  => now()->subDay(),
            'end_date'    => now()->addDays(7),
            'is_active'   => true,
        ]);

        // Subtotal = (2 * 4.00) + (1 * 3.00) = 11.00
        // Discount 10% = 1.10 -> Total = 9.90
        $payload = [
            'staff_id'      => $user->id,
            'customer_name' => 'Alice Customer',
            'notes'         => 'Less ice, oat milk',
            'discount_code' => 'WELCOME10',
            'items'         => [
                [
                    'product_id' => $espresso->id,
                    'quantity'   => 2,
                ],
                [
                    'product_id' => $croissant->id,
                    'quantity'   => 1,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post('/orders', $payload);

        $response->assertSessionHas('success');
        $response->assertSessionHasNoErrors();

        // 1. Verify Order
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(9.90, (float) $order->total_amount);
        $this->assertEquals('Alice Customer', $order->customer_name);
        $this->assertStringStartsWith('ORD-' . now()->format('Ymd') . '-', $order->order_number);

        // 2. Verify Order Items Snapshot
        $this->assertDatabaseHas('order_items', [
            'order_id'     => $order->id,
            'product_id'   => $espresso->id,
            'product_name' => 'Signature Espresso',
            'unit_price'   => 4.00,
            'quantity'     => 2,
            'subtotal'     => 8.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id'     => $order->id,
            'product_id'   => $croissant->id,
            'product_name' => 'Butter Croissant',
            'unit_price'   => 3.00,
            'quantity'     => 1,
            'subtotal'     => 3.00,
        ]);

        // 3. Verify Order Discount Record
        $this->assertDatabaseHas('order_discounts', [
            'order_id'       => $order->id,
            'discount_id'    => $discount->id,
            'code_used'      => 'WELCOME10',
            'amount_applied' => 1.10,
            'type'           => 'percentage',
            'value'          => 10.00,
        ]);

        // 4. Verify Discount used_count increment
        $this->assertEquals(1, $discount->fresh()->used_count);

        // 5. Verify Product Stock decrements
        $this->assertEquals(18, $espresso->fresh()->stock);
        $this->assertEquals(14, $croissant->fresh()->stock);
    }

    public function test_insufficient_stock_rolls_back_entire_order_and_leaves_stock_untouched(): void
    {
        $user = User::factory()->create();

        $rareBeans = Product::create([
            'name'     => 'Special Reserve Beans',
            'category' => 'Coffee Beans',
            'price'    => 35.00,
            'stock'    => 2,
        ]);

        $croissant = Product::create([
            'name'     => 'Almond Croissant',
            'category' => 'Pastries',
            'price'    => 4.00,
            'stock'    => 10,
        ]);

        $payload = [
            'staff_id'      => $user->id,
            'customer_name' => 'Bob',
            'items'         => [
                [
                    'product_id' => $croissant->id,
                    'quantity'   => 2,
                ],
                [
                    'product_id' => $rareBeans->id,
                    'quantity'   => 5, // Exceeds available stock of 2
                ],
            ],
        ];

        $response = $this->actingAs($user)->post('/orders', $payload);

        $response->assertSessionHasErrors('items');

        // Verify no order or order items were created
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

        // Verify stock was NOT altered on either item
        $this->assertEquals(2, $rareBeans->fresh()->stock);
        $this->assertEquals(10, $croissant->fresh()->stock);
    }

    public function test_invalid_or_expired_discount_code_is_rejected(): void
    {
        $user = User::factory()->create();

        $tea = Product::create([
            'name'     => 'Jasmine Green Tea',
            'category' => 'Tea & Beverages',
            'price'    => 3.50,
            'stock'    => 15,
        ]);

        $expiredDiscount = Discount::create([
            'name'        => 'Expired Flash Sale',
            'code'        => 'EXPIRED20',
            'type'        => 'percentage',
            'value'       => 20.00,
            'usage_limit' => 10,
            'used_count'  => 0,
            'start_date'  => now()->subDays(10),
            'end_date'    => now()->subDay(),
            'is_active'   => true,
        ]);

        $payload = [
            'staff_id'      => $user->id,
            'customer_name' => 'Charlie',
            'discount_code' => 'EXPIRED20',
            'items'         => [
                [
                    'product_id' => $tea->id,
                    'quantity'   => 1,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post('/orders', $payload);

        $response->assertSessionHasErrors('discount_code');
        $this->assertDatabaseCount('orders', 0);
        $this->assertEquals(15, $tea->fresh()->stock);
        $this->assertEquals(0, $expiredDiscount->fresh()->used_count);
    }

    public function test_product_snapshots_are_preserved_when_product_is_later_modified(): void
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name'     => 'Original Cold Brew',
            'category' => 'Coffee',
            'price'    => 4.00,
            'stock'    => 10,
        ]);

        $payload = [
            'staff_id'      => $user->id,
            'customer_name' => 'Dave',
            'items'         => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 2,
                ],
            ],
        ];

        $this->actingAs($user)->post('/orders', $payload);

        $orderItem = OrderItem::first();
        $this->assertNotNull($orderItem);
        $this->assertEquals('Original Cold Brew', $orderItem->product_name);
        $this->assertEquals(4.00, (float) $orderItem->unit_price);
        $this->assertEquals(8.00, (float) $orderItem->subtotal);

        // Modify the source product name and price
        $product->update([
            'name'  => 'Super Premium Nitro Cold Brew (Price Hike)',
            'price' => 7.50,
        ]);

        // Order item must retain original snapshotted values
        $freshOrderItem = $orderItem->fresh();
        $this->assertEquals('Original Cold Brew', $freshOrderItem->product_name);
        $this->assertEquals(4.00, (float) $freshOrderItem->unit_price);
        $this->assertEquals(8.00, (float) $freshOrderItem->subtotal);
    }

    public function test_cannot_delete_completed_or_paid_order(): void
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name'     => 'Americano',
            'category' => 'Coffee',
            'price'    => 3.00,
            'stock'    => 10,
        ]);

        $order = Order::create([
            'order_number'  => 'ORD-20260822-0099',
            'status'        => 'completed',
            'total_amount'  => 6.00,
            'staff_id'      => $user->id,
            'customer_name' => 'Elena',
        ]);

        $order->items()->create([
            'product_id'   => $product->id,
            'product_name' => 'Americano',
            'unit_price'   => 3.00,
            'quantity'     => 2,
            'subtotal'     => 6.00,
        ]);

        $response = $this->actingAs($user)->delete("/orders/{$order->id}");

        $response->assertSessionHasErrors('order');
        $this->assertDatabaseHas('orders', ['id' => $order->id]);
    }

    public function test_deleting_pending_order_restores_inventory(): void
    {
        $user = User::factory()->create();

        $product = Product::create([
            'name'     => 'Matcha Latte',
            'category' => 'Tea & Beverages',
            'price'    => 4.50,
            'stock'    => 8,
        ]);

        $order = Order::create([
            'order_number'  => 'ORD-20260822-0100',
            'status'        => 'pending',
            'total_amount'  => 9.00,
            'staff_id'      => $user->id,
            'customer_name' => 'Frank',
        ]);

        $order->items()->create([
            'product_id'   => $product->id,
            'product_name' => 'Matcha Latte',
            'unit_price'   => 4.50,
            'quantity'     => 2,
            'subtotal'     => 9.00,
        ]);

        $response = $this->actingAs($user)->delete("/orders/{$order->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);

        // Stock restored from 8 to 10
        $this->assertEquals(10, $product->fresh()->stock);
    }
}
