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
        foreach ($this->alerts() as $alert) {
            $alert->user->notify(new DailyDigestNotification(
                now()->copy()->setTime(7, 0)->subDays(1),
                now()->copy()->setTime(7, 0),
            ));

            $alert->update(['last_sent_at' => now()->toDateTimeString()]);
        }
    }

    private function alerts(): Collection
    {
        return Alert::with('user')
            ->where('type', 'daily-digest')
            ->whereDate('last_sent_at', '<>', now()->toDateString())
            ->get();
    }
}
