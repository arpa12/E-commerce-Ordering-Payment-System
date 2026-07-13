<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripePaymentGateway implements PaymentGatewayInterface
{
    protected string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret', 'mock_secret_key');
    }

    public function createPayment(Order $order): array
    {
        Log::info('Stripe create payment intent', ['order_id' => $order->id, 'amount' => $order->total_amount]);

        // If in test environment or mock key, return dummy/mock data
        if (app()->environment('testing') || $this->secretKey === 'mock_secret_key') {
            $paymentIntentId = 'pi_' . bin2hex(random_bytes(12));
            return [
                'success' => true,
                'transaction_id' => $paymentIntentId,
                'client_secret' => $paymentIntentId . '_secret_' . bin2hex(random_bytes(8)),
                'amount' => $order->total_amount,
                'provider' => 'stripe',
                'status' => 'pending',
                'raw_response' => ['mock' => true, 'id' => $paymentIntentId, 'status' => 'requires_payment_method']
            ];
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->asForm()
            ->post('https://api.stripe.com/v1/payment_intents', [
                'amount' => (int)($order->total_amount * 100), // convert to cents
                'currency' => 'usd',
                'metadata' => [
                    'order_id' => $order->id,
                ]
            ]);

        if ($response->failed()) {
            Log::error('Stripe PaymentIntent creation failed', ['response' => $response->json()]);
            throw new \Exception('Stripe API error: ' . ($response->json()['error']['message'] ?? 'Unknown error'));
        }

        $data = $response->json();

        return [
            'success' => true,
            'transaction_id' => $data['id'],
            'client_secret' => $data['client_secret'],
            'amount' => $order->total_amount,
            'provider' => 'stripe',
            'status' => 'pending',
            'raw_response' => $data
        ];
    }

    public function confirmPayment(array $data): array
    {
        $paymentIntentId = $data['payment_intent_id'] ?? null;
        Log::info('Stripe confirm payment intent', ['payment_intent_id' => $paymentIntentId]);

        if (!$paymentIntentId) {
            throw new \InvalidArgumentException('payment_intent_id is required');
        }

        if (app()->environment('testing') || $this->secretKey === 'mock_secret_key') {
            return [
                'success' => true,
                'transaction_id' => $paymentIntentId,
                'status' => 'success',
                'raw_response' => ['mock' => true, 'id' => $paymentIntentId, 'status' => 'succeeded']
            ];
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("https://api.stripe.com/v1/payment_intents/{$paymentIntentId}/confirm");

        if ($response->failed()) {
            Log::error('Stripe PaymentIntent confirmation failed', ['response' => $response->json()]);
            return [
                'success' => false,
                'transaction_id' => $paymentIntentId,
                'status' => 'failed',
                'raw_response' => $response->json()
            ];
        }

        $resData = $response->json();
        $status = ($resData['status'] === 'succeeded') ? 'success' : 'failed';

        return [
            'success' => $status === 'success',
            'transaction_id' => $resData['id'],
            'status' => $status,
            'raw_response' => $resData
        ];
    }

    public function queryPayment(string $transactionId): array
    {
        Log::info('Stripe query payment status', ['transaction_id' => $transactionId]);

        if (app()->environment('testing') || $this->secretKey === 'mock_secret_key') {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'success',
                'raw_response' => ['mock' => true, 'id' => $transactionId, 'status' => 'succeeded']
            ];
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("https://api.stripe.com/v1/payment_intents/{$transactionId}");

        if ($response->failed()) {
            Log::error('Stripe PaymentIntent query failed', ['response' => $response->json()]);
            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'failed',
                'raw_response' => $response->json()
            ];
        }

        $resData = $response->json();
        $status = ($resData['status'] === 'succeeded') ? 'success' : 'failed';

        return [
            'success' => $status === 'success',
            'transaction_id' => $resData['id'],
            'status' => $status,
            'raw_response' => $resData
        ];
    }
}
