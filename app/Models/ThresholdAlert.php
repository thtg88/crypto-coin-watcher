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
        'trend' => 'boolean',
        'user_id' => 'integer',
        'value' => 'integer',
    ];

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
