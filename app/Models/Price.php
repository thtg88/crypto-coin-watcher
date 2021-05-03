<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Price extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'coin_id',
        'currency_id',
        'value',
        'value_last_updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'float',
        'value_last_updated_at' => 'datetime',
    ];

    public function getValueLastUpdatedAtTimestamp(): int
    {
        return $this->value_last_updated_at->getTimestamp();
    }

    // ACCESSORS

    public function getValueAttribute(int $value): float
    {
        return $value / 100_000_000;
    }

    // MUTATORS

    public function setValueAttribute(float $value): void
    {
        $this->attributes['value'] = $value * 100_000_000;
    }

    // RELATIONSHIPS

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
