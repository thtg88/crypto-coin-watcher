<?php

namespace App\Notifications;

use App\Helpers\CurrencyHelper;
use App\Helpers\TrendHelper;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VariationPercentageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $period,
        private float $final_value,
        private string $coin_external_id,
        private float $variation_percentage,
        private int $seconds_between_alerts,
    ) {
    }

    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage())->markdown('mail.variation-percentage', [
            'coin_external_id' => $this->coin_external_id,
            'final_value' => (new CurrencyHelper($this->final_value))->format(),
            'notifiable' => $notifiable,
            'period' => $this->period,
            'seconds_between_alerts' => $this->seconds_between_alerts,
            'trend' => $this->trend(),
            'variation_percentage' => $this->format(),
        ]);
    }

    private function format(): string
    {
        return number_format($this->variation_percentage, 2);
    }

    private function trend(): string
    {
        return (new TrendHelper($this->variation_percentage > 0))->format();
    }
}
