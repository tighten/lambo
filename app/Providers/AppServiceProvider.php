<?php

namespace App\Providers;

use App\Services\QuestionsService;
use Illuminate\Support\Facades\File;
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
         * @TODO After adopting config strategy,most likely this singleton can be abandoned
         *      We may selectively change a config value from the launch options, and storing it's value
         *      right away in the config.
         */
        $this->app->singleton(QuestionsService::class, QuestionsService::class);
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
