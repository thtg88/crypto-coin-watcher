<?php

namespace App\Notifications;

use App\Models\Coin;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

abstract class DigestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Carbon $start, protected Carbon $end)
    {
    }

    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    protected function getData(): array
    {
        $data = [];

        foreach ($this->coins() as $coin) {
            foreach ($this->currencies() as $currency) {
                $data[] = $this->getRow($coin, $currency);
            }
        }

        return $data;
    }

    protected function getRow(Coin $coin, Currency $currency): array
    {
        $first = $this->firstAverage($coin, $currency);
        $last = $this->lastAverage($coin, $currency);

        return [
            'coin' => $coin->external_id,
            'currency' => $currency->symbol,
            'min' => $this->format($this->minAverage($coin, $currency)),
            'max' => $this->format($this->maxAverage($coin, $currency)),
            'first' => $this->format($first),
            'last' => $this->format($last),
            'trend' => $this->getTrend($first, $last),
            'variation_percentage' => $this->getVariationPercentage($first, $last),
        ];
    }

    protected function getVariationPercentage(float $first_value, float $last_value): string
    {
        if ($first_value === 0.0) {
            return '-';
        }

        $variation = $last_value - $first_value;
        $percentage = ($variation / $first_value) * 100;

        return number_format($percentage, 2);
    }

    protected function getTrend(float $first_value, float $last_value): string
    {
        if ($last_value > $first_value) {
            return '🟢';
        }

        return '🛑';
    }

    protected function format(float $value): string
    {
        if ($value >= 1000) {
            return number_format($value, 2);
        }

        return (string) $value;
    }

    protected function coins(): Collection
    {
        return Coin::enabled()->select('id', 'external_id')->get();
    }

    protected function currencies(): Collection
    {
        return Currency::select('id', 'symbol')->get();
    }

    protected function maxAverage(Coin $coin, Currency $currency): float
    {
        return $this->averageQuery($coin, $currency)
            ->max('value') / config('app.coin_price_storage_coefficient');
    }

    protected function minAverage(Coin $coin, Currency $currency): float
    {
        return $this->averageQuery($coin, $currency)
            ->min('value') / config('app.coin_price_storage_coefficient');
    }

    protected function firstAverage(Coin $coin, Currency $currency): float
    {
        return $this->averageQuery($coin, $currency)
            ->orderBy('from')
            ->select('value')
            ->first()
            ?->value ?? 0.0;
    }

    protected function lastAverage(Coin $coin, Currency $currency): float
    {
        return $this->averageQuery($coin, $currency)
            ->orderByDesc('to')
            ->select('value')
            ->first()
            ?->value ?? 0.0;
    }

    abstract protected function averageQuery(Coin $coin, Currency $currency): Builder;
}
