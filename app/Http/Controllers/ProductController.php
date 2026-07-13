<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // If not authenticated or not an admin, only show active products
        if (!$request->user() || !$request->user()->is_admin) {
            $query->where('status', 'active');
        }

        $products = $query->latest()->paginate(15);

        return response()->json($products);
    }

    /**
     * Display the specified product.
     */
    public function show(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            // Restrict viewing inactive products to admin only
            if ($product->status !== 'active' && (!$request->user() || !$request->user()->is_admin)) {
                return response()->json(['message' => 'Product not found.'], 404);
            }

            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Product not found.'], 404);
        }
    }

    /**
     * Store a newly created product in storage (Admin only).
     */
    public function store(Request $request)
    {
        Log::info('Admin product creation attempt', ['sku' => $request->input('sku')]);

        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product = Product::create($request->all());

            Log::info('Product created successfully', ['product_id' => $product->id, 'sku' => $product->sku]);

            return response()->json([
                'message' => 'Product created successfully',
                'product' => $product
            ], 201);
        } catch (\Exception $e) {
            Log::error('Product creation error', ['message' => $e->getMessage()]);
            return response()->json([
                'message' => 'An error occurred while creating the product.'
            ], 500);
        }
    }

    /**
     * Update the specified product in storage (Admin only).
     */
    public function update(Request $request, $id)
    {
        Log::info('Admin product update attempt', ['product_id' => $id]);

        try {
            $product = Product::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $id,
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'status' => 'sometimes|required|string|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product->update($request->all());

            Log::info('Product updated successfully', ['product_id' => $product->id, 'sku' => $product->sku]);

            return response()->json([
                'message' => 'Product updated successfully',
                'product' => $product
            ], 200);
        } catch (\Exception $e) {
            Log::error('Product update error', ['message' => $e->getMessage()]);
            return response()->json([
                'message' => 'An error occurred while updating the product.'
            ], 500);
        }
    }

    /**
     * Remove the specified product from storage (Admin only).
     */
    public function destroy($id)
    {
        Log::info('Admin product deletion attempt', ['product_id' => $id]);

        try {
            $product = Product::findOrFail($id);
            $product->delete();

            Log::info('Product deleted successfully', ['product_id' => $id]);

            return response()->json([
                'message' => 'Product deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Product deletion error', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'Product not found.'], 404);
        }
    }
}

