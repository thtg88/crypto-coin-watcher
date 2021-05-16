<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariationPercentageAlert extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'coin_id',
        'currency_id',
        'period',
        'threshold',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'coin_id' => 'integer',
        'currency_id' => 'integer',
        'threshold' => 'float',
        'user_id' => 'integer',
    ];

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
