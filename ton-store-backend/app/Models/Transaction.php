<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'asset_id',
        'transaction_hash',
        'from_address',
        'to_address',
        'amount',
        'type',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:9',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
} 