<?php

namespace App\Console\Commands;

use App\Caches\CurrenciesCache;
use App\Jobs\CalculateAveragesJob;
use App\Models\Coin;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RecalculateAveragesCommand extends Command
{
    private Collection $coins;
    private Carbon $now;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'averages:recalculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate averages from a pre-defined date to another one.';

    public function handle(): int
    {
        $cursor = $this->from()->copy();
        while ($cursor <= $this->to()) {
            dump("processing {$cursor}");

            foreach ($this->coins() as $coin_external_id) {
                dispatch(new CalculateAveragesJob(
                    $cursor,
                    $coin_external_id,
                    $this->currencies()->pluck('symbol')->toArray(),
                ));
            }

            $cursor = $cursor->copy()->addMinutes(2);

            sleep(5);
        }

        return 0;
    }

    private function from(): Carbon
    {
        return new Carbon('2021-05-16 21:15:00');
    }

    private function to(): Carbon
    {
        $this->now ??= now();

        return $this->now;
    }

    private function coins(): Collection
    {
        $this->coins ??= Coin::enabled()->pluck('external_id');

        return $this->coins;
    }

    private function currencies(): EloquentCollection
    {
        return (new CurrenciesCache(['gbp']))->get();
    }
}
