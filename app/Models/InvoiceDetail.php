<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'service_category_id',
        'name',
        'price',
        'quantity',
        'total_price',
    ];

    // Automatically calculate total_price before saving the model
    protected static function booted()
    {
        static::saving(function ($invoiceDetail) {
            $invoiceDetail->total_price = $invoiceDetail->price * $invoiceDetail->quantity;
        });
    }
}
