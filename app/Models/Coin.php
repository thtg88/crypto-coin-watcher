<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Coin extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'external_id',
        'name',
        'symbol',
    ];

    public function getLastPriceUpdatedAtTimestamp(): ?int
    {
        return $this->lastPrice?->getValueLastUpdatedAtTimestamp();
    }

    // SCOPES

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->whereIn('external_id', config('app.enabled_coins', []));
    }

    public function scopeWithLastPriceId(Builder $query): Builder
    {
        return $query->addSelect([
            'last_price_id' => Price::query()
                ->select('prices.id')
                ->whereColumn('coins.id', 'prices.coin_id')
                ->latest()
                ->take(1),
        ]);
    }

    // RELATIONSHIPS

    public function lastPrice(): BelongsTo
    {
        return $this->belongsTo(Price::class, 'last_price_id', 'id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }
}
