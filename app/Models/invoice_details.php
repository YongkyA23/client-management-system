<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class invoice_details extends Model
{

    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'service_name',
        'quantity',
        'service_price',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($invoiceDetail) {
            $invoiceDetail->total_price = $invoiceDetail->quantity * $invoiceDetail->service_price;
        });
    }
}
