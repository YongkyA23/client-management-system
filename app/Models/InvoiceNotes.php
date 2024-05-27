<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceNotes extends Model
{
    use HasFactory;
    protected $fillable = [
        'content'
    ];
}
