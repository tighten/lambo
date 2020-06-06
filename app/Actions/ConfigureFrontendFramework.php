<?php

namespace App\Actions;

use App\Shell;
use Illuminate\Support\Facades\Config;

class ConfigureFrontendFramework
{
    use LamboAction;

    private $shell;
    private $laravelUi;
    private $availableFrontends;

    public function __construct(Shell $shell, LaravelUi $laravelUi)
    {
        $this->shell = $shell;
        $this->laravelUi = $laravelUi;
    }

    public function __invoke(array $availableFrontends)
    {
        $this->availableFrontends = $availableFrontends;

        if (! Config::get('lambo.store.frontend')) {
            return;
        }

        app('console-writer')->logStep('Configuring frontend scaffolding');

        if ($this->continueInstallation()) {
            $this->laravelUi->install();

            $process = $this->shell->execInProject(sprintf("php artisan ui %s%s", config('lambo.store.frontend'), $this->extraOptions()));

            $this->abortIf(! $process->isSuccessful(), sprintf("Installation of %s UI scaffolding did not complete successfully.", config('lambo.store.frontend')), $process);

            app('console-writer')->success(config('lambo.store.frontend') . ' ui scaffolding installed.');
        }
    }

    private function continueInstallation(): bool
    {
        if ($this->validFrontend()) {
            return true;
        }

        $configuredFrontend = $this->chooseFrontend();
        if ($configuredFrontend !== 'none') {
            Config::set('lambo.store.frontend', $configuredFrontend);
            app('console-writer')->success("Using {$configuredFrontend} ui scaffolding.", ' OK ');
            return true;
        }

        app('console-writer')->success('No frontend framework will be installed.', ' OK ');
        return false;
    }

    private function chooseFrontend()
    {
        $this->availableFrontends[] = 'none';
        $message = sprintf("<fg=yellow>I can't install %s</>. Please choose one of the following options", Config::get('lambo.store.frontend'));
        $preselectedChoice = count($this->availableFrontends) - 1;

        return app('console')->choice($message, $this->availableFrontends, $preselectedChoice);
    }

    private function extraOptions()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }

    private function validFrontend()
    {
        return in_array(strtolower(Config::get('lambo.store.frontend')), $this->availableFrontends);
    }
}
