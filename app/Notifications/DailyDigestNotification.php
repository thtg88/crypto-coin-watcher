<?php

namespace App\Notifications;

use App\Models\Average;
use App\Models\Coin;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Messages\MailMessage;

class DailyDigestNotification extends DigestNotification
{
    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)->markdown('mail.digests.daily', [
            'data' => $this->getData(),
            'notifiable' => $notifiable,
            'start' => $this->start,
            'end' => $this->end,
        ]);
    }

    protected function averageQuery(Coin $coin, Currency $currency): Builder
    {
        return Average::where('from', '>=', $this->start->toDateTimeString())
            ->where('to', '<=', $this->end->toDateTimeString())
            ->where('coin_id', $coin->id)
            ->where('currency_id', $currency->id)
            ->where('period', '1 hours');
    }
}
