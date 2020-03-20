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
        if (function_exists('posix_getuid')) {
            // Mac or Linux
            $path = posix_getpwuid(posix_getuid())['dir'];
        } else {
            // Windows
            $path = exec('echo %USERPROFILE%');
        }

        config()->set([
            'home_dir' => $path,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
