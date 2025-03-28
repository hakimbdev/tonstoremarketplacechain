<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image_url',
        'price',
        'owner_address',
        'contract_address',
        'metadata',
        'status',
        'auction_end_time',
    ];

    protected $casts = [
        'metadata' => 'array',
        'price' => 'decimal:9',
        'auction_end_time' => 'datetime',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
} 