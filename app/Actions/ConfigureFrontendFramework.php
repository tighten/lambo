<?php


namespace App\Actions;


use App\Shell;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ConfigureFrontendFramework
{
    const FRAMEWORKS = [
        'vue',
        'react',
        'bootstrap',
    ];

    private $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (! config('lambo.store.frontend')) {
            return;
        }

        $this->ensureLaravelUiInstalled();

        $this->shell->execInProject('php artisan ui ' . config('lambo.store.frontend'));
        app('console')->info('[ artisan ] ui scaffold set to ' . config('lambo.store.frontend'));
    }

    public function ensureLaravelUiInstalled()
    {
        $composeConfig = json_decode(File::get(config('lambo.store.project_path') . '/composer.json'), true);

        if (! Arr::has($composeConfig, 'require.laravel/ui')) {
            $this->shell->execInProject('composer require laravel/ui');
        }
    }
}
