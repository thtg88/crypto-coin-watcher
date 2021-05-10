<?php

namespace App\Notifications;

use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class DailyDigestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Carbon $start, private Carbon $end)
    {
    }

    public function via(User $notifiable)
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $data = [];
        foreach ($this->coins() as $coin) {
            foreach ($this->currencies() as $currency) {
                $data[] = $this->getRow($coin, $currency);
            }
        }

        return (new MailMessage)->markdown('mail.digests.daily', [
            'data' => $data,
            'notifiable' => $notifiable,
        ]);
    }

    private function getRow(Coin $coin, Currency $currency): array
    {
        return [
            'coin' => $coin->external_id,
            'currency' => $currency->symbol,
            'min' => $this->format($this->minHourlyAverage($coin, $currency)),
            'max' => $this->format($this->maxHourlyAverage($coin, $currency)),
            'first' => $this->format($this->firstHourlyAverage($coin, $currency)),
            'last' => $this->format($this->lastHourlyAverage($coin, $currency)),
        ];
    }

    private function format(float $value): string
    {
        if ($value >= 1000) {
            return number_format($value, 2);
        }

        return (string) $value;
    }

    private function coins(): Collection
    {
        return Coin::enabled()->select('id', 'external_id')->get();
    }

    private function currencies(): Collection
    {
        return Currency::select('id', 'symbol')->get();
    }

    private function maxHourlyAverage(Coin $coin, Currency $currency): float
    {
        return $this->hourlyAveragesQuery($coin, $currency)
            ->max('value') / config('app.coin_storage_coefficient');
    }

    private function minHourlyAverage(Coin $coin, Currency $currency): float
    {
        return $this->hourlyAveragesQuery($coin, $currency)
            ->min('value') / config('app.coin_storage_coefficient');
    }

    private function firstHourlyAverage(Coin $coin, Currency $currency): float
    {
        return $this->hourlyAveragesQuery($coin, $currency)
            ->orderBy('from')
            ->select('value')
            ->first()
            ?->value ?? 0.0;
    }

    private function lastHourlyAverage(Coin $coin, Currency $currency): float
    {
        return $this->hourlyAveragesQuery($coin, $currency)
            ->orderByDesc('to')
            ->select('value')
            ->first()
            ?->value ?? 0.0;
    }

    private function hourlyAveragesQuery(Coin $coin, Currency $currency): Builder
    {
        return Average::where('from', '>=', $this->start->toDateTimeString())
            ->where('to', '<=', $this->end->toDateTimeString())
            ->where('coin_id', $coin->id)
            ->where('currency_id', $currency->id)
            ->where('period', '1 hours');
    }
}
