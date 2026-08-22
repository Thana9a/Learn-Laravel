<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_order_screen_renders_with_products_and_categories()
    {
        Product::create([
            'name'        => 'Espresso Roast',
            'category'    => 'Coffee',
            'price'       => 3.50,
            'stock'       => 20,
            'description' => 'Rich and bold espresso.',
            'is_featured' => true,
        ]);

        $response = $this->get('/pos');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Pos/Menu/Index')
            ->has('products', 1)
            ->has('categories', 1)
        );
    }

    public function test_authenticated_dashboard_renders_dashboard_screen()
    {
        $user = User::factory()->create();

        Product::create([
            'name'     => 'Cappuccino',
            'category' => 'Coffee',
            'price'    => 5.00,
            'stock'    => 12,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('recentOrders')
            ->has('stats')
        );
    }

    public function test_can_create_pos_order_successfully_and_decrements_stock()
    {
        $user = User::factory()->create();

        $latte = Product::create([
            'name'     => 'Vanilla Latte',
            'category' => 'Coffee',
            'price'    => 4.50,
            'stock'    => 10,
        ]);

        $muffin = Product::create([
            'name'     => 'Blueberry Muffin',
            'category' => 'Pastries',
            'price'    => 3.00,
            'stock'    => 5,
        ]);

        $payload = [
            'staff_id'      => $user->id,
            'customer_name' => 'John Barista',
            'notes'         => 'Oat milk, extra hot',
            'items'         => [
                [
                    'product_id' => $latte->id,
                    'quantity'   => 2,
                ],
                [
                    'product_id' => $muffin->id,
                    'quantity'   => 1,
                ],
            ],
        ];

        $response = $this->post('/pos/orders', $payload);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        // Check Order record
        $this->assertDatabaseHas('orders', [
            'staff_id'      => $user->id,
            'customer_name' => 'John Barista',
            'notes'         => 'Oat milk, extra hot',
            'total_amount'  => 12.00, // (2 * 4.50) + (1 * 3.00) = 12.00
            'status'        => 'pending',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertStringStartsWith('ORD-', $order->order_number);

        // Check OrderItems snapshots
        $this->assertDatabaseHas('order_items', [
            'order_id'     => $order->id,
            'product_id'   => $latte->id,
            'product_name' => 'Vanilla Latte',
            'unit_price'   => 4.50,
            'quantity'     => 2,
            'subtotal'     => 9.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id'     => $order->id,
            'product_id'   => $muffin->id,
            'product_name' => 'Blueberry Muffin',
            'unit_price'   => 3.00,
            'quantity'     => 1,
            'subtotal'     => 3.00,
        ]);

        // Check stock decrements
        $this->assertEquals(8, $latte->fresh()->stock);
        $this->assertEquals(4, $muffin->fresh()->stock);
    }

    public function test_cannot_create_order_when_quantity_exceeds_available_stock()
    {
        $product = Product::create([
            'name'     => 'Rare Geisha Beans',
            'category' => 'Coffee Beans',
            'price'    => 25.00,
            'stock'    => 2,
        ]);

        $payload = [
            'customer_name' => 'Alice',
            'items'         => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 5, // Exceeds available 2
                ],
            ],
        ];

        $response = $this->post('/pos/orders', $payload);

        $response->assertSessionHasErrors('items');

        // Verify no order created and stock untouched
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertEquals(2, $product->fresh()->stock);
    }

    public function test_order_fails_validation_if_empty_items_provided()
    {
        $payload = [
            'customer_name' => 'Bob',
            'items'         => [],
        ];

        $response = $this->post('/pos/orders', $payload);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('orders', 0);
    }
}
