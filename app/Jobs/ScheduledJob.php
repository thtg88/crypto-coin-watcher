<?php

namespace App\Jobs;

use App\Jobs\Middleware\ScheduleNext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

abstract class ScheduledJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function scheduleNext(?Carbon $next_executes_at = null): void
    {
        if (!config('app.scheduled_jobs.enabled')) {
            return;
        }

        $next_executes_at ??= $this->nextExecutesAt();
        // Do not reschedule if no timestamp
        if ($next_executes_at === null) {
            return;
        }

        static::dispatch(...$this->getNextArgs())->delay($next_executes_at);
    }

    public function middleware(): array
    {
        return [new ScheduleNext];
    }

    protected function getNextArgs(): array
    {
        return [];
    }

    abstract protected function nextExecutesAt(): ?Carbon;
}
