<?php

namespace App\Providers;

use App\Providers\TelescopeServiceProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as BaseTelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(BaseTelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        // DB::listen(static function ($query) {
        //     dump(
        //         $query->sql,
        //         $query->bindings,
        //         // $query->time
        //     );
        // });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
