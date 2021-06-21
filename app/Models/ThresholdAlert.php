<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThresholdAlert extends Model
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
        'seconds_between_alerts',
        'trend',
        'user_id',
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
        'seconds_between_alerts' => 'integer',
        'trend' => 'boolean',
        'user_id' => 'integer',
        'value' => 'integer',
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
