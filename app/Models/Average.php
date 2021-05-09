<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Average extends Model
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
        'from',
        'period',
        'to',
        'value',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'coin_id' => 'integer',
        'currency_id' => 'integer',
        'from' => 'datetime',
        'to' => 'datetime',
        'value' => 'float',
    ];

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
