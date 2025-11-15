<?php

namespace VisioSoft\LaraAnsible;

use Illuminate\Support\ServiceProvider;

class LaraAnsibleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laraansible.php',
            'laraansible'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/laraansible.php' => config_path('laraansible.php'),
        ], 'laraansible-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laraansible-migrations');
    }
}
