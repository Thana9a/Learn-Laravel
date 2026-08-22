<?php

namespace Tests\Feature;

use App\Models\Discount;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_login_returns_valid_token_and_user(): void
    {
        $user = User::factory()->create([
            'email'    => 'barista@coffeeshop.com',
            'password' => 'secret123',
        ]);

        $response = $this->postJson('/api/login', [
            'email'       => 'barista@coffeeshop.com',
            'password'    => 'secret123',
            'device_name' => 'flutter-tablet-pos',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email'],
        ]);

        $this->assertNotEmpty($response->json('token'));
        $this->assertEquals('barista@coffeeshop.com', $response->json('user.email'));
    }

    public function test_token_login_rejects_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'barista@coffeeshop.com',
            'password' => 'correct-password',
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'barista@coffeeshop.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_token_can_access_protected_api_routes(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-client')->plainTextToken;

        Product::create([
            'name'     => 'Iced Caramel Macchiato',
            'category' => 'Coffee',
            'price'    => 4.75,
            'stock'    => 15,
        ]);

        // Protected /api/user
        $responseUser = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/user');

        $responseUser->assertStatus(200);
        $responseUser->assertJsonPath('user.id', $user->id);

        // Protected /api/products/search
        $responseSearch = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/products/search?query=Macchiato');

        $responseSearch->assertStatus(200);
        $responseSearch->assertJsonCount(1, 'data');
        $responseSearch->assertJsonPath('data.0.name', 'Iced Caramel Macchiato');
    }

    public function test_token_can_validate_discount_codes(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-client')->plainTextToken;

        Discount::create([
            'name'        => 'VIP 15%',
            'code'        => 'VIP15',
            'type'        => 'percentage',
            'value'       => 15.00,
            'usage_limit' => 100,
            'used_count'  => 0,
            'is_active'   => true,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/discounts/validate', [
                'code'     => 'VIP15',
                'subtotal' => 20.00,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid' => true,
            'discount' => [
                'code'           => 'VIP15',
                'amount_applied' => 3.00,
                'new_total'      => 17.00,
            ],
        ]);
    }

    public function test_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-client')->plainTextToken;

        // Logout
        $responseLogout = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        $responseLogout->assertStatus(200);
        $responseLogout->assertJsonPath('message', __('auth.logged_out'));

        // Subsequent access fails with 401 Unauthorized
        $this->app['auth']->forgetGuards();
        $responseSubsequent = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/user');

        $responseSubsequent->assertStatus(401);
    }
}
