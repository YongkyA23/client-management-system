<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'notes',
        'total',
        'issue_date',
        'due_date',
        'paid_date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice_details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }


    use HasFactory;
}
