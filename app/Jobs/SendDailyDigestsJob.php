<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Notifications\DailyDigestNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Database\Eloquent\Collection;

class SendDailyDigestsJob extends Job
{
    public function handle(): void
    {
        // do not resend if there already alerts sent for this day
        if ($this->shouldntSend()) {
            return;
        }

        foreach ($this->alerts() as $alert) {
            $alert->user->notify(new DailyDigestNotification(
                now()->copy()->setTime(8, 0)->subDays(1),
                now()->copy()->setTime(8, 0),
            ));

            $alert->update(['last_sent_at' => now()->toDateTimeString()]);
        }
    }

    private function shouldntSend(): bool
    {
        // If there are existing alerts sent today
        return Alert::where('type', 'daily-digest')
            ->whereDate('last_sent_at', now()->toDateString())
            ->exists();
    }

    private function alerts(): Collection
    {
        return Alert::where('type', 'daily-digest')->get();
    }
}
