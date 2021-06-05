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
        $alerts = $this->alerts();

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
        return Alert::with('user')
            ->where('type', 'weekly-digest')
            ->where(static function ($query) {
                $last_sent_week = DB::raw("EXTRACT('week' FROM last_sent_at)");

                $query->orWhereNull('last_sent_at')
                    ->orWhere($last_sent_week, '<>', now()->weekOfYear);
            })
            ->get();
    }
}
