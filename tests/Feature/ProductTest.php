<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products()
    {
        Product::create([
            'name' => 'Caramel Macchiato Test',
            'category' => 'Coffee',
            'price' => 4.99,
            'stock' => 10,
        ]);

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Caramel Macchiato Test');
    }

    public function test_can_create_product()
    {
        $response = $this->post('/products', [
            'name' => 'Iced Matcha Test',
            'category' => 'Tea & Beverages',
            'price' => 5.25,
            'stock' => 50,
            'description' => 'Test matcha drink description',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'name' => 'Iced Matcha Test',
            'price' => 5.25,
        ]);
    }

    public function test_can_update_product()
    {
        $product = Product::create([
            'name' => 'Old Muffin Name',
            'category' => 'Pastries',
            'price' => 3.50,
            'stock' => 20,
        ]);

        $response = $this->put("/products/{$product->id}", [
            'name' => 'New Muffin Name',
            'category' => 'Pastries',
            'price' => 3.25,
            'stock' => 25,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Muffin Name',
            'price' => 3.25,
            'stock' => 25,
        ]);
    }

    public function test_can_delete_product()
    {
        $product = Product::create([
            'name' => 'Delete Me Coffee',
            'category' => 'Coffee Beans',
            'price' => 18.00,
            'stock' => 5,
        ]);

        $response = $this->delete("/products/{$product->id}");

        $response->assertRedirect('/products');
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
