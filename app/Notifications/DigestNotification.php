<?php

namespace App\Notifications;

use App\Helpers\AverageVariationHelper;
use App\Helpers\CurrencyHelper;
use App\Helpers\TrendHelper;
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

    private Collection $currencies;

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

    abstract protected function averageQuery(Coin $coin, Currency $currency): Builder;

    private function getRow(Coin $coin, Currency $currency): array
    {
        $first = $this->firstAverage($coin, $currency);
        $last = $this->lastAverage($coin, $currency);
        $variation = (new AverageVariationHelper($first, $last))
            ->formattedPercentage();

        return [
            'coin' => $coin->external_id,
            'currency' => $currency->symbol,
            'min' => $this->format($this->minAverage($coin, $currency)),
            'max' => $this->format($this->maxAverage($coin, $currency)),
            'first' => $this->format($first),
            'last' => $this->format($last),
            'trend' => $this->getTrend($first, $last),
            'variation_percentage' => $variation,
        ];
    }

    private function getTrend(float $first_value, float $last_value): string
    {
        return (new TrendHelper($last_value > $first_value))->format();
    }

    private function format(float $value): string
    {
        return (new CurrencyHelper($value))->format();
    }

    private function coins(): Collection
    {
        return Coin::enabled()->select('id', 'external_id')->get();
    }

    private function currencies(): Collection
    {
        $this->currencies ??= Currency::select('id', 'symbol')->get();

        return $this->currencies;
    }

    private function maxAverage(Coin $coin, Currency $currency): float
    {
        return $this->averageQuery($coin, $currency)
            ->max('value') / config('app.coin_price_storage_coefficient');
    }

    private function minAverage(Coin $coin, Currency $currency): float
    {
        return $this->averageQuery($coin, $currency)
            ->min('value') / config('app.coin_price_storage_coefficient');
    }

    private function firstAverage(Coin $coin, Currency $currency): float
    {
        return $this->averageQuery($coin, $currency)
            ->orderBy('from')
            ->select('value')
            ->first()
            ?->value ?? 0.0;
    }

    private function lastAverage(Coin $coin, Currency $currency): float
    {
        return $this->averageQuery($coin, $currency)
            ->orderByDesc('to')
            ->select('value')
            ->first()
            ?->value ?? 0.0;
    }
}
