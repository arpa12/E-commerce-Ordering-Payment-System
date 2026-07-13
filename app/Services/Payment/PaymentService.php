<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Initiate payment for an order.
     */
    public function checkout(Order $order, string $provider): array
    {
        if ($order->status === 'paid') {
            throw new \Exception('Order has already been paid.');
        }

        $gateway = PaymentGatewayFactory::make($provider);
        $result = $gateway->createPayment($order);

        if ($result['success']) {
            Payment::create([
                'order_id' => $order->id,
                'provider' => $provider,
                'transaction_id' => $result['transaction_id'],
                'amount' => $order->total_amount,
                'status' => 'pending',
                'raw_response' => $result['raw_response']
            ]);
        }

        return $result;
    }

    /**
     * Process payment completion (confirm/execute status updates and safe stock reduction).
     */
    public function completePayment(string $transactionId, string $provider, array $gatewayResult): array
    {
        Log::info('Processing payment completion', [
            'transaction_id' => $transactionId,
            'provider' => $provider,
            'status' => $gatewayResult['status']
        ]);

        return DB::transaction(function () use ($transactionId, $provider, $gatewayResult) {
            $payment = Payment::where('transaction_id', $transactionId)
                ->where('provider', $provider)
                ->first();

            if (!$payment) {
                // If payment record wasn't created yet (e.g. Stripe direct webhook before intent callback), find order from raw response or metadata
                $orderId = $gatewayResult['raw_response']['metadata']['order_id'] ?? null;
                if (!$orderId) {
                    throw new \Exception("Payment record not found and order reference missing in metadata.");
                }

                $payment = Payment::create([
                    'order_id' => $orderId,
                    'provider' => $provider,
                    'transaction_id' => $transactionId,
                    'amount' => $gatewayResult['amount'] ?? 0.0,
                    'status' => 'pending',
                ]);
            }

            $order = Order::lockForUpdate()->findOrFail($payment->order_id);

            if ($gatewayResult['status'] === 'success') {
                if ($order->status !== 'paid') {
                    // Safely reduce product stock after payment confirmation
                    foreach ($order->items as $item) {
                        $product = Product::lockForUpdate()->findOrFail($item->product_id);

                        if ($product->stock < $item->quantity) {
                            Log::warning("Insufficient stock during payment completion", [
                                'order_id' => $order->id,
                                'product_id' => $product->id,
                                'stock' => $product->stock,
                                'requested' => $item->quantity
                            ]);
                            throw new \Exception("Insufficient stock for product '{$product->name}' to complete this order.");
                        }

                        $product->decrement('stock', $item->quantity);
                        Log::info("Stock reduced successfully", [
                            'product_id' => $product->id,
                            'new_stock' => $product->stock
                        ]);
                    }

                    $order->update(['status' => 'paid']);
                }

                $payment->update([
                    'status' => 'success',
                    'raw_response' => $gatewayResult['raw_response']
                ]);
            } else {
                // Payment failed or was canceled
                if ($order->status === 'pending') {
                    $order->update(['status' => 'canceled']);
                }

                $payment->update([
                    'status' => 'failed',
                    'raw_response' => $gatewayResult['raw_response']
                ]);
            }

            return [
                'order' => $order,
                'payment' => $payment
            ];
        });
    }
}
