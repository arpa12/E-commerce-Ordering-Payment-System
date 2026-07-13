<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('executes the complete Stripe order flow successfully', function () {
    // Step 1: User selects products -> creates order
    $user = User::factory()->create();
    $product1 = Product::factory()->create([
        'name' => 'T-Shirt',
        'price' => 15.00,
        'stock' => 20,
        'status' => 'active'
    ]);
    $product2 = Product::factory()->create([
        'name' => 'Jeans',
        'price' => 45.00,
        'stock' => 10,
        'status' => 'active'
    ]);

    // Create Order via API
    $orderResponse = actingAs($user, 'sanctum')->postJson('/api/orders', [
        'items' => [
            ['product_id' => $product1->id, 'quantity' => 2], // Subtotal: 30.00
            ['product_id' => $product2->id, 'quantity' => 1], // Subtotal: 45.00
        ]
    ]);

    $orderResponse->assertStatus(201);
    $orderId = $orderResponse->json('order.id');
    $this->assertEquals('pending', $orderResponse->json('order.status'));
    $this->assertEquals(75.00, $orderResponse->json('order.total_amount'));

    // Step 2: User chooses the payment provider (Stripe) -> system initiates payment
    $checkoutResponse = actingAs($user, 'sanctum')->postJson('/api/payments/checkout', [
        'order_id' => $orderId,
        'provider' => 'stripe'
    ]);

    $checkoutResponse->assertStatus(200);
    $transactionId = $checkoutResponse->json('transaction_id');
    $this->assertNotNull($transactionId);

    // Step 3: Provider confirms payment (via Webhook)
    $webhookResponse = postJson('/api/payments/stripe/webhook', [
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => $transactionId,
                'amount' => 7500,
                'metadata' => [
                    'order_id' => $orderId
                ]
            ]
        ]
    ]);

    $webhookResponse->assertStatus(200);

    // Step 4: Order status updates accordingly and stock is reduced
    $this->assertEquals('paid', Order::find($orderId)->status);

    $payment = Payment::where('transaction_id', $transactionId)->first();
    $this->assertEquals('success', $payment->status);

    // Stock verification
    $this->assertEquals(18, $product1->fresh()->stock); // 20 - 2 = 18
    $this->assertEquals(9, $product2->fresh()->stock);  // 10 - 1 = 9
});

it('executes the complete bKash order flow successfully', function () {
    // Step 1: User selects products -> creates order
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Smart Watch',
        'price' => 120.00,
        'stock' => 5,
        'status' => 'active'
    ]);

    // Create Order
    $orderResponse = actingAs($user, 'sanctum')->postJson('/api/orders', [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 3] // Total: 360.00
        ]
    ]);
    $orderResponse->assertStatus(201);
    $orderId = $orderResponse->json('order.id');

    // Step 2: User chooses the payment provider (bKash) -> system initiates payment
    $checkoutResponse = actingAs($user, 'sanctum')->postJson('/api/payments/checkout', [
        'order_id' => $orderId,
        'provider' => 'bkash'
    ]);
    $checkoutResponse->assertStatus(200);
    $transactionId = $checkoutResponse->json('transaction_id');

    // Step 3: Provider confirms payment (via execute endpoint)
    $executeResponse = actingAs($user, 'sanctum')->postJson('/api/payments/bkash/execute', [
        'payment_id' => $transactionId
    ]);
    $executeResponse->assertStatus(200);

    // Step 4: Order status updates accordingly and stock is reduced
    $this->assertEquals('paid', Order::find($orderId)->status);
    $this->assertEquals('success', Payment::where('transaction_id', $transactionId)->first()->status);

    // Stock verification
    $this->assertEquals(2, $product->fresh()->stock); // 5 - 3 = 2
});
