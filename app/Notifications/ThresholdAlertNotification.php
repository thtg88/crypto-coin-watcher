<?php

namespace App\Notifications;

use App\Helpers\CurrencyHelper;
use App\Helpers\TrendHelper;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThresholdAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $coin_external_id,
        private string $currency_symbol,
        private float $threshold,
        private bool $trend,
        private float $value,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param \App\Models\User $notifiable
     * @return array
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param \App\Models\User $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage())->markdown('mail.threshold-alert', [
            'coin_external_id' => $this->coin_external_id,
            'currency_symbol' => $this->currency_symbol,
            'notifiable' => $notifiable,
            'threshold' => (new CurrencyHelper($this->threshold))->format(),
            'trend' => $this->trend,
            'trend_symbol' => (new TrendHelper($this->trend))->format(),
            'value' => (new CurrencyHelper($this->value))->format(),
        ]);
    }
}
