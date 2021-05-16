<?php

namespace App\Actions;

use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\Price;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class CalculatePeriodCurrencyAverageAction
{
    private array $data;
    private Carbon $pricesFrom;
    private Carbon $pricesTo;

    public function __construct(
        private Coin $coin,
        private Currency $currency,
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
        if ($average === null) {
            return;
        }

        $average = Average::create(array_merge(
            $this->data(),
            ['value' => (int) $this->average()],
        ));
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

        $this->pricesFrom ??= now()->$method($this->value);

        return $this->pricesFrom->toDateTimeString();
    }

    private function pricesTo(): string
    {
        $this->pricesTo ??= now();

        return $this->pricesTo->toDateTimeString();
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
