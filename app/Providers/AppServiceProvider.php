<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        config()->set('lambo.store.install_path', getcwd());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
