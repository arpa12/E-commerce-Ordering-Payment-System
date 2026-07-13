<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashPaymentGateway implements PaymentGatewayInterface
{
    protected string $appKey;
    protected string $appSecret;
    protected string $username;
    protected string $password;
    protected string $baseUrl;

    public function __construct()
    {
        $this->appKey = config('services.bkash.app_key', 'mock_app_key');
        $this->appSecret = config('services.bkash.app_secret', 'mock_app_secret');
        $this->username = config('services.bkash.username', 'mock_username');
        $this->password = config('services.bkash.password', 'mock_password');
        $this->baseUrl = config('services.bkash.base_url', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta');
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    protected function getGrantToken(): string
    {
        if (app()->environment('testing') || $this->appKey === 'mock_app_key') {
            return 'mock_id_token';
        }

        $response = Http::withHeaders($this->getHeaders())
            ->post("{$this->baseUrl}/tokenized/checkout/token/grant", [
                'app_key' => $this->appKey,
                'app_secret' => $this->appSecret,
            ]);

        if ($response->failed() || !isset($response->json()['id_token'])) {
            Log::error('bKash token generation failed', ['response' => $response->json()]);
            throw new \Exception('bKash authentication failed: ' . ($response->json()['errorMessage'] ?? 'Unknown error'));
        }

        return $response->json()['id_token'];
    }

    public function createPayment(Order $order): array
    {
        Log::info('bKash create payment initiation', ['order_id' => $order->id, 'amount' => $order->total_amount]);

        if (app()->environment('testing') || $this->appKey === 'mock_app_key') {
            $paymentId = 'TR00' . bin2hex(random_bytes(6));
            return [
                'success' => true,
                'transaction_id' => $paymentId,
                'checkout_url' => "https://mock.bkash.com/payment/checkout?paymentID={$paymentId}",
                'amount' => $order->total_amount,
                'provider' => 'bkash',
                'status' => 'pending',
                'raw_response' => ['mock' => true, 'paymentID' => $paymentId, 'transactionStatus' => 'Initiated']
            ];
        }

        $token = $this->getGrantToken();

        $response = Http::withHeaders(array_merge($this->getHeaders(), [
            'Authorization' => $token,
            'X-APP-Key' => $this->appKey
        ]))->post("{$this->baseUrl}/tokenized/checkout/create", [
            'mode' => '0011',
            'payerReference' => 'Order-' . $order->id,
            'callbackURL' => route('bkash.callback'),
            'amount' => number_format($order->total_amount, 2, '.', ''),
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => 'INV-' . $order->id
        ]);

        if ($response->failed() || !isset($response->json()['paymentID'])) {
            Log::error('bKash payment creation failed', ['response' => $response->json()]);
            throw new \Exception('bKash create payment failed: ' . ($response->json()['errorMessage'] ?? 'Unknown error'));
        }

        $data = $response->json();

        return [
            'success' => true,
            'transaction_id' => $data['paymentID'],
            'checkout_url' => $data['bkashURL'] ?? null,
            'amount' => $order->total_amount,
            'provider' => 'bkash',
            'status' => 'pending',
            'raw_response' => $data
        ];
    }

    public function confirmPayment(array $data): array
    {
        // For bKash, confirmation means executing the payment after user verification
        $paymentId = $data['payment_intent_id'] ?? $data['bkash_payment_id'] ?? null;
        Log::info('bKash execute payment', ['payment_id' => $paymentId]);

        if (!$paymentId) {
            throw new \InvalidArgumentException('payment_id is required');
        }

        if (app()->environment('testing') || $this->appKey === 'mock_app_key') {
            return [
                'success' => true,
                'transaction_id' => $paymentId,
                'status' => 'success',
                'raw_response' => ['mock' => true, 'paymentID' => $paymentId, 'transactionStatus' => 'Completed']
            ];
        }

        $token = $this->getGrantToken();

        $response = Http::withHeaders(array_merge($this->getHeaders(), [
            'Authorization' => $token,
            'X-APP-Key' => $this->appKey
        ]))->post("{$this->baseUrl}/tokenized/checkout/execute", [
            'paymentID' => $paymentId
        ]);

        $resData = $response->json();

        if ($response->failed() || (isset($resData['statusCode']) && $resData['statusCode'] !== '0000')) {
            Log::error('bKash payment execution failed', ['response' => $resData]);
            return [
                'success' => false,
                'transaction_id' => $paymentId,
                'status' => 'failed',
                'raw_response' => $resData
            ];
        }

        return [
            'success' => true,
            'transaction_id' => $resData['paymentID'],
            'status' => 'success',
            'raw_response' => $resData
        ];
    }

    public function queryPayment(string $transactionId): array
    {
        Log::info('bKash query payment', ['payment_id' => $transactionId]);

        if (app()->environment('testing') || $this->appKey === 'mock_app_key') {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'success',
                'raw_response' => ['mock' => true, 'paymentID' => $transactionId, 'transactionStatus' => 'Completed']
            ];
        }

        $token = $this->getGrantToken();

        $response = Http::withHeaders(array_merge($this->getHeaders(), [
            'Authorization' => $token,
            'X-APP-Key' => $this->appKey
        ]))->post("{$this->baseUrl}/tokenized/checkout/query", [
            'paymentID' => $transactionId
        ]);

        $resData = $response->json();

        if ($response->failed() || (isset($resData['statusCode']) && $resData['statusCode'] !== '0000')) {
            Log::error('bKash payment query failed', ['response' => $resData]);
            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'status' => 'failed',
                'raw_response' => $resData
            ];
        }

        $status = ($resData['transactionStatus'] === 'Completed') ? 'success' : 'pending';

        return [
            'success' => $status === 'success',
            'transaction_id' => $resData['paymentID'],
            'status' => $status,
            'raw_response' => $resData
        ];
    }
}
