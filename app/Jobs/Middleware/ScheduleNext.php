<?php

namespace App\Jobs\Middleware;

use App\Jobs\ScheduledJob;

class ScheduleNext
{
    /**
     * Process the queued job.
     *
     * @param \App\Jobs\ScheduledJob $job
     * @param callable $next
     * @return mixed
     */
    public function handle(ScheduledJob $job, $next)
    {
        $response = $next($job);

        $job->scheduleNext();

        return $response;
    }
}
