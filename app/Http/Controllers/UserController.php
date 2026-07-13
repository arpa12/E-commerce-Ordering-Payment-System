<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get the authenticated user's orders.
     */
    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->with('items.product')->latest()->paginate(15);

        return response()->json($orders);
    }

    /**
     * Get the authenticated user's payments.
     */
    public function payments(Request $request)
    {
        $payments = Payment::whereIn('order_id', $request->user()->orders()->pluck('id'))
            ->latest()
            ->paginate(15);

        return response()->json($payments);
    }
}


