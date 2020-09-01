<?php

namespace App\Actions;

use App\Shell;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class LaravelUi
{
    use LamboAction;

    private $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function install()
    {
        if ($this->laravelUiInstalled()) {
            return;
        }

        app('console-writer')->note('To use Laravel frontend scaffolding the composer package laravel/ui is required. Installing now...');

        $process = $this->shell->execInProject("composer require laravel/ui{$this->withQuiet()}");

        $this->abortIf(! $process->isSuccessful(), "Installation of laravel/ui did not complete successfully.", $process);

        app('console-writer')->success('laravel/ui installed.');
    }

    private function laravelUiInstalled(): bool
    {
        $composeConfig = json_decode(File::get(config('lambo.store.project_path') . '/composer.json'), true);
        return Arr::has($composeConfig, 'require.laravel/ui');
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
