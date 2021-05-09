<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Price extends Model
{
    use Concerns\WithValueAccessor;
    use Concerns\WithValueMutator;

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
        'coin_id' => 'integer',
        'currency_id' => 'integer',
        'value' => 'float',
        'value_last_updated_at' => 'datetime',
    ];

    public function getValueLastUpdatedAtTimestamp(): int
    {
        return $this->value_last_updated_at->getTimestamp();
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
