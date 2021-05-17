<?php

namespace App\Actions;

use App\Jobs\SendVariationPercentageNotificationsJob;
use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\Price;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class CalculatePeriodCurrencyAverageAction
{
    private array $data;

    public function __construct(
        private Coin $coin,
        private Currency $currency,
        private Carbon $prices_to,
        private int $value,
        private string $period,
    ) {
    }

    public function __invoke(): void
    {
        if (!$this->shouldCalculate()) {
            return;
        }

        $average = Average::firstWhere($this->data());
        if ($average !== null) {
            return;
        }

        $average = Average::create(array_merge(
            $this->data(),
            ['value' => (int) $this->average()],
        ));

        if ($average->period === SendVariationPercentageNotificationsJob::PROCESSABLE_PERIOD) {
            dispatch(new SendVariationPercentageNotificationsJob($average));
        }
    }

    private function data(): array
    {
        $this->data ??= [
            'coin_id' => $this->coin->id,
            'currency_id' => $this->currency->id,
            'period' => $this->fullPeriod(),
            'from' => $this->from(),
            'to' => $this->to(),
        ];

        return $this->data;
    }

    private function shouldCalculate(): bool
    {
        return Price::query()->where(
            'value_last_updated_at',
            '<',
            $this->pricesFrom()
        )->exists();
    }

    private function from(): string
    {
        return $this->baseQuery()->min('value_last_updated_at');
    }

    private function to(): string
    {
        return $this->baseQuery()->max('value_last_updated_at');
    }

    private function average(): float
    {
        return (float) $this->baseQuery()->average('value');
    }

    private function baseQuery(): Builder
    {
        return Price::query()
            ->where('currency_id', $this->currency->id)
            ->where('coin_id', $this->coin->id)
            ->whereBetween('value_last_updated_at', [
                $this->pricesFrom(),
                $this->pricesTo(),
            ]);
    }

    private function pricesFrom(): string
    {
        $method = $this->getCarbonMethod();

        return $this->prices_to->copy()
            ->$method($this->value)
            ->toDateTimeString();
    }

    private function pricesTo(): string
    {
        return $this->prices_to->toDateTimeString();
    }

    private function getCarbonMethod(): string
    {
        return 'sub'.Str::title($this->period);
    }

    private function fullPeriod(): string
    {
        return implode(' ', [(string) $this->value, $this->period]);
    }
}
