<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ConfigureFrontendFramework
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $configuredFrontend = config('lambo.store.frontend');

        if (is_null($configuredFrontend)) {
            return;
        }

        $this->consoleWriter->logStep('Configuring frontend scaffolding');

        $chosenFrontend = $this->validatedFrontendChoice($configuredFrontend);

        if ($chosenFrontend === 'none') {
            return $this->consoleWriter->verbose()->ok('No frontend framework will be installed.');
        }

        $this->ensureJetstreamInstalled();

        $process = $this->shell->execInProject(sprintf(
            "php artisan jetstream:install %s%s%s",
            $chosenFrontend,
            config('lambo.store.with_teams') ? ' --teams' : '',
            config('lambo.store.with_output') ? '' : ' --quiet'
        ));

        $this->abortIf(! $process->isSuccessful(), "Installation of {$chosenFrontend} UI scaffolding did not complete successfully.", $process);

        if ($chosenFrontend === 'inertia') {
            /* @todo temporary manual creation
                   This will be fixed when binding of App\ConsoleWriter and Shell
                   into the container is implemented. */

            app(InstallNpmDependencies::class,[
                'shell' => $this->shell,
                'consoleWriter' => $this->consoleWriter
            ])();

            app(CompileAssets::class, [
                'shell' => $this->shell,
                'consoleWriter' => $this->consoleWriter
            ])();
        }

        $this->consoleWriter->verbose()->success( "{$chosenFrontend} UI scaffolding installed.");
    }

    private function validatedFrontendChoice(string $configuredFrontend): string
    {
        $availableFrontends = ['inertia', 'livewire', 'none'];

        if (in_array(strtolower($configuredFrontend), $availableFrontends)) {
            return $configuredFrontend;
        }

        $configuredFrontend = app('console')->choice("<fg=yellow>{$configuredFrontend}</> is not a framework I can install. Please choose one of the following options", $availableFrontends, count($availableFrontends) - 1);
        config(['lambo.store.frontend' => $configuredFrontend]);

        $this->consoleWriter->verbose()->ok("Using {$configuredFrontend} ui scaffolding.");

        return $configuredFrontend;
    }

    public function ensureJetstreamInstalled()
    {
        $composerConfig = json_decode(File::get(config('lambo.store.project_path') . '/composer.json'), true);
        if (Arr::has($composerConfig, 'require.laravel/jetstream')) {
            return;
        }

        $this->consoleWriter->verbose()->note('Installing required composer package laravel/jetstream.');

        $process = $this->shell->execInProject('composer require laravel/jetstream' . (config('lambo.store.with_output') ? '' : ' --quiet'));

        $this->abortIf(! $process->isSuccessful(), "Installation of laravel/jetstream did not complete successfully.", $process);

        $this->consoleWriter->verbose()->success('laravel/jetstream installed.');
    }
}
