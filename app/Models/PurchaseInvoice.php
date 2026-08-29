<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;
use App\Models\PurchaseInvoiceItem;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'purchase_date',
        'subtotal',
        'tax_amount',
        'grand_total'
    ];

    public function supplier()
{
    return $this->belongsTo(
        Supplier::class,
        'supplier_id'
    );
}

public function items()
{
    return $this->hasMany(
        PurchaseInvoiceItem::class,
        'purchase_invoice_id'
    );
}
}