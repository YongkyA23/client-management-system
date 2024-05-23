<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    protected $fillable = [
        'name',
        'description',
        'phone',
        'email',
        'address',
    ];

    use HasFactory;
}
