<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Initialize/Create a payment checkout session or intent.
     */
    public function createPayment(Order $order): array;

    /**
     * Handle manual confirmation or webhook notifications.
     */
    public function confirmPayment(array $data): array;

    /**
     * Query status of a payment transaction.
     */
    public function queryPayment(string $transactionId): array;
}
