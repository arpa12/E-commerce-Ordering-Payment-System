<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'transaction_id',
        'amount',
        'status',
        'raw_response',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'raw_response' => 'array',
        ];
    }

    /**
     * Get the order that the payment is for.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

