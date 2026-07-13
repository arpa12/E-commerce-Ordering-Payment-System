<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('requires authentication to initiate checkout', function () {
    postJson('/api/payments/checkout', [
        'order_id' => 1,
        'provider' => 'stripe'
    ])->assertStatus(401);
});

it('initiates checkout successfully for stripe and bkash', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 50.00,
        'stock' => 10,
        'status' => 'active'
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'total_amount' => 50.00,
        'status' => 'pending'
    ]);
    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 50.00,
        'subtotal' => 50.00
    ]);

    // Test Stripe checkout
    $responseStripe = actingAs($user, 'sanctum')->postJson('/api/payments/checkout', [
        'order_id' => $order->id,
        'provider' => 'stripe'
    ]);
    $responseStripe->assertStatus(200)->assertJsonFragment([
        'provider' => 'stripe',
        'status' => 'pending'
    ]);
    $stripeTxnId = $responseStripe->json('transaction_id');
    $this->assertDatabaseHas('payments', [
        'order_id' => $order->id,
        'provider' => 'stripe',
        'transaction_id' => $stripeTxnId,
        'status' => 'pending'
    ]);

    // Test bKash checkout
    $responseBkash = actingAs($user, 'sanctum')->postJson('/api/payments/checkout', [
        'order_id' => $order->id,
        'provider' => 'bkash'
    ]);
    $responseBkash->assertStatus(200)->assertJsonFragment([
        'provider' => 'bkash',
        'status' => 'pending'
    ]);
    $bkashTxnId = $responseBkash->json('transaction_id');
    $this->assertDatabaseHas('payments', [
        'order_id' => $order->id,
        'provider' => 'bkash',
        'transaction_id' => $bkashTxnId,
        'status' => 'pending'
    ]);
});

it('restricts checkout initiation to the owner of the order', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $order = Order::create([
        'user_id' => $user1->id,
        'total_amount' => 50.00,
        'status' => 'pending'
    ]);

    actingAs($user2, 'sanctum')->postJson('/api/payments/checkout', [
        'order_id' => $order->id,
        'provider' => 'stripe'
    ])->assertStatus(403);
});

it('confirms stripe payment manually and reduces stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 100.00,
        'stock' => 10,
        'status' => 'active'
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'total_amount' => 100.00,
        'status' => 'pending'
    ]);
    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 100.00,
        'subtotal' => 200.00
    ]);

    // Create checkout first to register transaction_id
    $checkoutResponse = actingAs($user, 'sanctum')->postJson('/api/payments/checkout', [
        'order_id' => $order->id,
        'provider' => 'stripe'
    ]);
    $transactionId = $checkoutResponse->json('transaction_id');

    // Confirm Payment
    $confirmResponse = actingAs($user, 'sanctum')->postJson('/api/payments/stripe/confirm', [
        'payment_intent_id' => $transactionId
    ]);

    $confirmResponse->assertStatus(200);
    $confirmResponse->assertJsonPath('order.status', 'paid');
    $confirmResponse->assertJsonPath('payment.status', 'success');

    // Verify stock is decremented (10 - 2 = 8)
    $this->assertEquals(8, $product->fresh()->stock);
});

it('processes stripe webhook successfully and reduces stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 100.00,
        'stock' => 10,
        'status' => 'active'
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'total_amount' => 100.00,
        'status' => 'pending'
    ]);
    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 100.00,
        'subtotal' => 100.00
    ]);

    // Create checkout to register transaction
    $checkoutResponse = actingAs($user, 'sanctum')->postJson('/api/payments/checkout', [
        'order_id' => $order->id,
        'provider' => 'stripe'
    ]);
    $transactionId = $checkoutResponse->json('transaction_id');

    // Trigger webhook
    $webhookResponse = postJson('/api/payments/stripe/webhook', [
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => $transactionId,
                'amount' => 10000,
                'metadata' => [
                    'order_id' => $order->id
                ]
            ]
        ]
    ]);

    $webhookResponse->assertStatus(200);

    $this->assertEquals('paid', $order->fresh()->status);
    $this->assertEquals(9, $product->fresh()->stock);
});

it('processes stripe webhook failure and cancels order', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 100.00,
        'stock' => 10,
        'status' => 'active'
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'total_amount' => 100.00,
        'status' => 'pending'
    ]);
    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 100.00,
        'subtotal' => 100.00
    ]);

    // Create checkout to register transaction
    $checkoutResponse = actingAs($user, 'sanctum')->postJson('/api/payments/checkout', [
        'order_id' => $order->id,
        'provider' => 'stripe'
    ]);
    $transactionId = $checkoutResponse->json('transaction_id');

    // Trigger webhook
    $webhookResponse = postJson('/api/payments/stripe/webhook', [
        'type' => 'payment_intent.payment_failed',
        'data' => [
            'object' => [
                'id' => $transactionId,
                'amount' => 10000,
                'metadata' => [
                    'order_id' => $order->id
                ]
            ]
        ]
    ]);

    $webhookResponse->assertStatus(200);

    $this->assertEquals('canceled', $order->fresh()->status);
    // Stock must not change
    $this->assertEquals(10, $product->fresh()->stock);
});

it('executes bkash payment successfully and reduces stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 200.00,
        'stock' => 10,
        'status' => 'active'
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'total_amount' => 200.00,
        'status' => 'pending'
    ]);
    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 3,
        'price' => 200.00,
        'subtotal' => 600.00
    ]);

    // Create checkout to register transaction
    $checkoutResponse = actingAs($user, 'sanctum')->postJson('/api/payments/checkout', [
        'order_id' => $order->id,
        'provider' => 'bkash'
    ]);
    $transactionId = $checkoutResponse->json('transaction_id');

    // Execute payment
    $executeResponse = actingAs($user, 'sanctum')->postJson('/api/payments/bkash/execute', [
        'payment_id' => $transactionId
    ]);

    $executeResponse->assertStatus(200);
    $this->assertEquals('paid', $order->fresh()->status);
    $this->assertEquals(7, $product->fresh()->stock);
});

it('prevents double stock reduction on double webhook or callbacks', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'price' => 100.00,
        'stock' => 10,
        'status' => 'active'
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'total_amount' => 100.00,
        'status' => 'pending'
    ]);
    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 100.00,
        'subtotal' => 100.00
    ]);

    $checkoutResponse = actingAs($user, 'sanctum')->postJson('/api/payments/checkout', [
        'order_id' => $order->id,
        'provider' => 'stripe'
    ]);
    $transactionId = $checkoutResponse->json('transaction_id');

    // First Webhook Call
    postJson('/api/payments/stripe/webhook', [
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => $transactionId,
                'metadata' => ['order_id' => $order->id]
            ]
        ]
    ])->assertStatus(200);

    $this->assertEquals(9, $product->fresh()->stock);

    // Second Webhook Call
    postJson('/api/payments/stripe/webhook', [
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => $transactionId,
                'metadata' => ['order_id' => $order->id]
            ]
        ]
    ])->assertStatus(200);

    // Stock should remain 9 (not decremented again)
    $this->assertEquals(9, $product->fresh()->stock);
});
