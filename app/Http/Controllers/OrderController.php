<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display the specified order.
     */
    public function show(Request $request, $id)
    {
        try {
            $order = Order::with('items.product')->findOrFail($id);

            // Ensure the user owns the order or is an admin
            if ($order->user_id !== $request->user()->id && !$request->user()->is_admin) {
                return response()->json([
                    'message' => 'Forbidden. You do not have permission to view this order.'
                ], 403);
            }

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Order not found.'], 404);
        }
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        Log::info('Order creation attempt', ['user_id' => $request->user()->id]);

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                $totalAmount = 0.0;
                $orderItemsData = [];

                foreach ($request->input('items') as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    // Verify product is active
                    if ($product->status !== 'active') {
                        return response()->json([
                            'message' => "Product '{$product->name}' is currently unavailable."
                        ], 422);
                    }

                    // Verify sufficient stock is available
                    if ($product->stock < $item['quantity']) {
                        return response()->json([
                            'message' => "Insufficient stock for product '{$product->name}'."
                        ], 422);
                    }

                    // Deterministic calculation of subtotal
                    $subtotal = round((float)$product->price * (int)$item['quantity'], 2);
                    $totalAmount += $subtotal;

                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => (float)$product->price,
                        'subtotal' => $subtotal,
                    ];
                }

                $totalAmount = round($totalAmount, 2);

                // Create Order
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                // Create Order Items
                foreach ($orderItemsData as $itemData) {
                    $order->items()->create($itemData);
                }

                Log::info('Order created successfully', ['order_id' => $order->id, 'total_amount' => $totalAmount]);

                return response()->json([
                    'message' => 'Order created successfully',
                    'order' => $order->load('items.product')
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error('Order creation error', [
                'user_id' => $request->user()->id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'An error occurred while creating the order.'
            ], 500);
        }
    }
}

