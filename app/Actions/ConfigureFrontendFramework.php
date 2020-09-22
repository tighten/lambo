<?php

namespace App\Actions;

use App\Shell;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ConfigureFrontendFramework
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        $configuredFrontend = config('lambo.store.frontend');

        if ($configuredFrontend === 'none') {
            return;
        }

        app('console-writer')->logStep("Installing {$configuredFrontend} UI scaffolding");

        $this->ensureJetstreamInstalled();

        $process = $this->shell->execInProject(sprintf(
            "php artisan jetstream:install %s%s%s",
            $configuredFrontend,
            config('lambo.store.teams') ? ' --teams' : '',
            config('lambo.store.with_output') ? '' : ' --quiet'
        ));

        $this->abortIf(! $process->isSuccessful(), "Installation of {$configuredFrontend} UI scaffolding did not complete successfully.", $process);

        if ($configuredFrontend === 'inertia') {
            app(InstallNpmDependencies::class)();
            app(CompileAssets::class)();
        }

        app('console-writer')->verbose()->success("{$configuredFrontend} UI scaffolding installed.");
    }

    public function ensureJetstreamInstalled()
    {
        $composerConfig = json_decode(File::get(config('lambo.store.project_path') . '/composer.json'), true);
        if (Arr::has($composerConfig, 'require.laravel/jetstream')) {
            return;
        }

        app('console-writer')->verbose()->note('Installing required composer package laravel/jetstream.');

        $process = $this->shell->execInProject('composer require laravel/jetstream' . (config('lambo.store.with_output') ? '' : ' --quiet'));

        $this->abortIf(! $process->isSuccessful(), "Installation of laravel/jetstream did not complete successfully.", $process);

        app('console-writer')->verbose()->success('laravel/jetstream installed.');
    }
}
