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
            $this->mergeConfigFrom($filePath, 'lambo');
        }

        $filePath = $homeFolder . '/.lambo/after.php';

        if (File::exists($filePath)) {
            $this->mergeConfigFrom($filePath, 'lambo.after');
        }
    }
}
