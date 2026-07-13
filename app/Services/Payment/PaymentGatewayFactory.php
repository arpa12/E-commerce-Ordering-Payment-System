<?php

namespace App\Services\Payment;

use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * Resolve the payment gateway implementation.
     */
    public static function make(string $provider): PaymentGatewayInterface
    {
        return match (strtolower($provider)) {
            'stripe' => app(StripePaymentGateway::class),
            'bkash' => app(BkashPaymentGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment provider: {$provider}"),
        };
    }
}
