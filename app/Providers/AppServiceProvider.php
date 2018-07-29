<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use App\Actions\CustomizeConfigRuntime;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerConfig();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**
         * @TODO Still needed to assess if the performance improvement is significant
         */
        $this->app->singleton(CustomizeConfigRuntime::class, CustomizeConfigRuntime::class);
    }

    /**
     * Register config
     */
    protected function registerConfig(): void
    {
        $homeFolder = $_SERVER['HOME'];

        $filePath = $homeFolder . '/.lambo/config.php';

        if (File::exists($filePath)) {
            /**
             * @TODO why is it not overriding the existing configs?? Shouldn't it?
             */
//            $this->mergeConfigFrom($filePath, 'lambo');

            $this->app['config']->set('lambo', array_merge(config('lambo'), require $filePath));
        }

        $filePath = $homeFolder . '/.lambo/after.php';

        if (File::exists($filePath)) {
            /**
             * @TODO why is it not overriding the existing configs?? Shouldn't it?
             */
//            $this->mergeConfigFrom($filePath, 'lambo-after');

            $this->app['config']->set('lambo-after', array_merge(config('lambo-after'), require $filePath));
        }
    }
}
