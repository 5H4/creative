<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Actor;
use App\Observers\ActorObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Actor::observe(ActorObserver::class);
    }
}
