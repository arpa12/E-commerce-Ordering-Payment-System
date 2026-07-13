<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayFactory;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Initiate payment checkout (Create Payment Intent / Create bKash Payment).
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
            'provider' => 'required|string|in:stripe,bkash',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $order = Order::findOrFail($request->input('order_id'));

            // Ensure the user owns the order
            if ($order->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Forbidden. You do not own this order.'
                ], 403);
            }

            $result = $this->paymentService->checkout($order, $request->input('provider'));

            return response()->json($result, 200);
        } catch (\Exception $e) {
            Log::error('Payment checkout error', ['message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Confirm Stripe payment manually (for client integration flows).
     */
    public function confirmStripe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $gateway = PaymentGatewayFactory::make('stripe');
            $gatewayResult = $gateway->confirmPayment([
                'payment_intent_id' => $request->input('payment_intent_id')
            ]);

            $result = $this->paymentService->completePayment(
                $request->input('payment_intent_id'),
                'stripe',
                $gatewayResult
            );

            return response()->json([
                'message' => 'Payment confirmed successfully',
                'order' => $result['order'],
                'payment' => $result['payment']
            ], 200);
        } catch (\Exception $e) {
            Log::error('Stripe payment confirmation error', ['message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle Stripe Webhook updates.
     */
    public function webhookStripe(Request $request)
    {
        Log::info('Stripe webhook received');

        // Extract payload
        $payload = $request->all();
        $type = $payload['type'] ?? '';

        if ($type === 'payment_intent.succeeded' || $type === 'payment_intent.payment_failed') {
            $object = $payload['data']['object'] ?? [];
            $paymentIntentId = $object['id'] ?? null;
            $status = ($type === 'payment_intent.succeeded') ? 'success' : 'failed';

            if ($paymentIntentId) {
                try {
                    $gatewayResult = [
                        'success' => $status === 'success',
                        'transaction_id' => $paymentIntentId,
                        'status' => $status,
                        'raw_response' => $payload
                    ];

                    $this->paymentService->completePayment($paymentIntentId, 'stripe', $gatewayResult);
                    return response()->json(['message' => 'Webhook processed successfully.'], 200);
                } catch (\Exception $e) {
                    Log::error('Stripe webhook handling error', ['message' => $e->getMessage()]);
                    return response()->json(['message' => $e->getMessage()], 500);
                }
            }
        }

        return response()->json(['message' => 'Ignored event.'], 200);
    }

    /**
     * Execute bKash checkout payment.
     */
    public function executeBkash(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $gateway = PaymentGatewayFactory::make('bkash');
            $gatewayResult = $gateway->confirmPayment([
                'bkash_payment_id' => $request->input('payment_id')
            ]);

            $result = $this->paymentService->completePayment(
                $request->input('payment_id'),
                'bkash',
                $gatewayResult
            );

            return response()->json([
                'message' => 'bKash payment executed successfully',
                'order' => $result['order'],
                'payment' => $result['payment']
            ], 200);
        } catch (\Exception $e) {
            Log::error('bKash payment execution error', ['message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Query bKash checkout payment.
     */
    public function queryBkash(Request $request, $transactionId)
    {
        try {
            $gateway = PaymentGatewayFactory::make('bkash');
            $gatewayResult = $gateway->queryPayment($transactionId);

            $result = $this->paymentService->completePayment(
                $transactionId,
                'bkash',
                $gatewayResult
            );

            return response()->json([
                'message' => 'bKash payment queried successfully',
                'order' => $result['order'],
                'payment' => $result['payment']
            ], 200);
        } catch (\Exception $e) {
            Log::error('bKash payment query error', ['message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}

