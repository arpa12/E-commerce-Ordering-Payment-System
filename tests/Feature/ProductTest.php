<?php

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows public users to list active products', function () {
    Product::factory()->create([
        'name' => 'Active Product',
        'sku' => 'ACT-1',
        'status' => 'active',
        'price' => 100.00,
        'stock' => 10,
    ]);

    Product::factory()->create([
        'name' => 'Inactive Product',
        'sku' => 'INACT-1',
        'status' => 'inactive',
        'price' => 200.00,
        'stock' => 5,
    ]);

    $response = getJson('/api/products');
    $response->assertStatus(200);

    $data = $response->json('data');
    $this->assertCount(1, $data);
    $this->assertEquals('Active Product', $data[0]['name']);
});

it('allows public users to view active product details, but restricts inactive products', function () {
    $active = Product::factory()->create([
        'name' => 'Active Product',
        'sku' => 'ACT-2',
        'status' => 'active',
        'price' => 100.00,
        'stock' => 10,
    ]);

    $inactive = Product::factory()->create([
        'name' => 'Inactive Product',
        'sku' => 'INACT-2',
        'status' => 'inactive',
        'price' => 200.00,
        'stock' => 5,
    ]);

    getJson("/api/products/{$active->id}")->assertStatus(200)->assertJsonFragment(['name' => 'Active Product']);
    getJson("/api/products/{$inactive->id}")->assertStatus(404);
});

it('restricts CRUD operations to authenticated users', function () {
    postJson('/api/products', [])->assertStatus(401);
    putJson('/api/products/1', [])->assertStatus(401);
    deleteJson('/api/products/1')->assertStatus(401);
});

it('restricts CRUD operations to admin users only', function () {
    $user = User::factory()->create(['is_admin' => false]);

    actingAs($user, 'sanctum')->postJson('/api/products', [])->assertStatus(403);
    actingAs($user, 'sanctum')->putJson('/api/products/1', [])->assertStatus(403);
    actingAs($user, 'sanctum')->deleteJson('/api/products/1')->assertStatus(403);
});

it('allows admin to create, update, and delete products', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    // Test Creation
    $createResponse = actingAs($admin, 'sanctum')->postJson('/api/products', [
        'name' => 'Admin Product',
        'sku' => 'ADM-1',
        'price' => 150.00,
        'stock' => 50,
        'status' => 'active',
        'description' => 'Created by admin',
    ]);

    $createResponse->assertStatus(201);
    $productId = $createResponse->json('product.id');
    $this->assertDatabaseHas('products', ['sku' => 'ADM-1']);

    // Test Update
    $updateResponse = actingAs($admin, 'sanctum')->putJson("/api/products/{$productId}", [
        'name' => 'Admin Product Updated',
        'price' => 175.50,
    ]);

    $updateResponse->assertStatus(200);
    $this->assertDatabaseHas('products', [
        'id' => $productId,
        'name' => 'Admin Product Updated',
        'price' => 175.50,
    ]);

    // Test Deletion
    actingAs($admin, 'sanctum')->deleteJson("/api/products/{$productId}")->assertStatus(200);
    $this->assertDatabaseMissing('products', ['id' => $productId]);
});
