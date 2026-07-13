<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('requires authentication to create or view orders', function () {
    postJson('/api/orders', [])->assertStatus(401);
    getJson('/api/orders/1')->assertStatus(401);
});

it('validates order input requirements', function () {
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson('/api/orders', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['items']);

    actingAs($user, 'sanctum')
        ->postJson('/api/orders', ['items' => 'not-an-array'])
        ->assertStatus(422);
});

it('creates an order successfully with correct totals', function () {
    $user = User::factory()->create();

    $product1 = Product::factory()->create([
        'name' => 'Product One',
        'price' => 10.50,
        'stock' => 100,
        'status' => 'active',
    ]);

    $product2 = Product::factory()->create([
        'name' => 'Product Two',
        'price' => 20.00,
        'stock' => 50,
        'status' => 'active',
    ]);

    $response = actingAs($user, 'sanctum')->postJson('/api/orders', [
        'items' => [
            ['product_id' => $product1->id, 'quantity' => 2],
            ['product_id' => $product2->id, 'quantity' => 3],
        ]
    ]);

    $response->assertStatus(201);

    // Expected subtotal 1: 10.50 * 2 = 21.00
    // Expected subtotal 2: 20.00 * 3 = 60.00
    // Expected total: 81.00
    $expectedTotal = 81.00;

    $response->assertJsonFragment([
        'total_amount' => $expectedTotal,
        'status' => 'pending',
    ]);

    $orderId = $response->json('order.id');
    $this->assertDatabaseHas('orders', [
        'id' => $orderId,
        'total_amount' => $expectedTotal,
        'status' => 'pending',
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $orderId,
        'product_id' => $product1->id,
        'quantity' => 2,
        'price' => 10.50,
        'subtotal' => 21.00,
    ]);

    $this->assertDatabaseHas('order_items', [
        'order_id' => $orderId,
        'product_id' => $product2->id,
        'quantity' => 3,
        'price' => 20.00,
        'subtotal' => 60.00,
    ]);
});

it('fails order creation if any product is inactive', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create([
        'status' => 'inactive',
        'stock' => 10,
    ]);

    actingAs($user, 'sanctum')->postJson('/api/orders', [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 1]
        ]
    ])->assertStatus(422);
});

it('fails order creation if product stock is insufficient', function () {
    $user = User::factory()->create();

    $product = Product::factory()->create([
        'status' => 'active',
        'stock' => 5,
    ]);

    actingAs($user, 'sanctum')->postJson('/api/orders', [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 6]
        ]
    ])->assertStatus(422);
});

it('enforces ownership check on viewing order details', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);

    $order = Order::create([
        'user_id' => $user1->id,
        'total_amount' => 100.00,
        'status' => 'pending',
    ]);

    // User 1 can view their own order
    actingAs($user1, 'sanctum')->getJson("/api/orders/{$order->id}")->assertStatus(200);

    // User 2 cannot view User 1's order
    actingAs($user2, 'sanctum')->getJson("/api/orders/{$order->id}")->assertStatus(403);

    // Admin can view any order
    actingAs($admin, 'sanctum')->getJson("/api/orders/{$order->id}")->assertStatus(200);
});
