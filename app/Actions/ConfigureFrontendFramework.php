<?php

namespace App\Actions;

use App\Shell\Shell;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ConfigureFrontendFramework
{
    use LamboAction;

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

        $this->logStep('Configuring frontend scaffolding');

        $process = $this->shell->execInProject('php artisan ui ' . config('lambo.store.frontend'));

        $this->abortIf(! $process->isSuccessful(), "Installation of UI scaffolding did not complete successfully.", $process);

        $this->info('UI scaffolding has been set to ' . config('lambo.store.frontend'));
    }

    public function ensureLaravelUiInstalled()
    {
        if ($this->laravelUiInstalled()) {
            return;
        }

        $this->logStep('To use Laravel frontend scaffolding composer package laravel/ui is required. Installing now.');

        $process = $this->shell->execInProject('composer require laravel/ui --quiet');

        $this->abortIf(! $process->isSuccessful(), "Installation of laravel/ui did not complete successfully.", $process);

        $this->info('laravel/ui installation installed.');
    }

    private function laravelUiInstalled(): bool
    {
        $composeConfig = json_decode(File::get(config('lambo.store.project_path') . '/composer.json'), true);
        return Arr::has($composeConfig, 'require.laravel/ui');
    }
}
