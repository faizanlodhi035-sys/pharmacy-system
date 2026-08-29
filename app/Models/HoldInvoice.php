<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoldInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'user_id',
        'customer_id',
        'customer_name',
        'cart_data',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'cart_data' => 'array',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
