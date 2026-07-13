<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('registers a user successfully', function () {
    $response = postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure([
                 'message',
                 'access_token',
                 'token_type',
                 'user' => ['id', 'name', 'email', 'created_at', 'updated_at']
             ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);
});

it('fails registration validation with duplicate email', function () {
    User::factory()->create([
        'email' => 'john@example.com',
    ]);

    $response = postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email']);
});

it('logs in a user successfully with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = postJson('/api/login', [
        'email' => 'john@example.com',
        'password' => 'secret123',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'message',
                 'access_token',
                 'token_type',
                 'user'
             ]);
});

it('fails to log in with incorrect credentials', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = postJson('/api/login', [
        'email' => 'john@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
             ->assertJson([
                 'message' => 'Invalid email or password',
             ]);
});

it('restricts access to orders and payments for unauthenticated users', function () {
    getJson('/api/orders')->assertStatus(401);
    getJson('/api/payments')->assertStatus(401);
});

it('allows authenticated users to retrieve their own orders and payments', function () {
    $user = User::factory()->create();

    // Create a mock order and payment for the user
    $order = Order::create([
        'user_id' => $user->id,
        'total_amount' => 150.00,
        'status' => 'pending',
    ]);

    $payment = Payment::create([
        'order_id' => $order->id,
        'provider' => 'bkash',
        'transaction_id' => 'TXN-12345',
        'amount' => 150.00,
        'status' => 'successful',
    ]);

    // Create another user's order to verify boundaries
    $otherUser = User::factory()->create();
    $otherOrder = Order::create([
        'user_id' => $otherUser->id,
        'total_amount' => 300.00,
        'status' => 'pending',
    ]);

    $response = actingAs($user, 'sanctum')->getJson('/api/orders');
    $response->assertStatus(200);

    $data = $response->json('data');
    $this->assertCount(1, $data);
    $this->assertEquals($order->id, $data[0]['id']);

    $responsePayment = actingAs($user, 'sanctum')->getJson('/api/payments');
    $responsePayment->assertStatus(200);

    $paymentData = $responsePayment->json('data');
    $this->assertCount(1, $paymentData);
    $this->assertEquals($payment->id, $paymentData[0]['id']);
});
