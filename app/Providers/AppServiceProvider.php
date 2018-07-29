<?php

namespace App\Providers;

use App\Interactive\OptionManager;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\AliasLoader;
use App\Actions\CustomizeConfigRuntime;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configFiles();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(CustomizeConfigRuntime::class, CustomizeConfigRuntime::class);

        $this->app->singleton(OptionManager::class, OptionManager::class);
        $this->app->alias(OptionManager::class, 'options');
        $loader = AliasLoader::getInstance();
        $loader->alias('Options', OptionManager::class);
    }

    /**
     * Apply config files
     *
     * @return void
     */
    protected function configFiles(): void
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
