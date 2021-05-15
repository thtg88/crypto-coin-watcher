<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Notifications\WeeklyDigestNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SendWeeklyDigestsJob extends Job
{
    public function handle(): void
    {
        foreach ($this->alerts() as $alert) {
            $alert->user->notify(new WeeklyDigestNotification(
                now()->copy()->setTime(9, 0)->subDays(7),
                now()->copy()->setTime(9, 0),
            ));

            $alert->update(['last_sent_at' => now()->toDateTimeString()]);
        }
    }

    private function alerts(): Collection
    {
        $last_sent_week = DB::raw("EXTRACT('week' FROM last_sent_at)");

        return Alert::where('type', 'weekly-digest')
            ->where($last_sent_week, '<>', now()->weekOfYear)
            ->get();
    }
}
