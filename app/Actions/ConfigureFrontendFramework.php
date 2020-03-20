<?php

namespace App\Actions;

use App\Shell\Shell;

class ConfigureFrontendFramework
{
    use LamboAction;

    private $shell;

    private $laravelUi;

    public function __construct(Shell $shell, LaravelUi $laravelUi)
    {
        $this->shell = $shell;
        $this->laravelUi = $laravelUi;
    }

    public function __invoke()
    {
        if (! config('lambo.store.frontend')) {
            return;
        }

        $this->laravelUi->install();

        $this->logStep('Configuring frontend scaffolding');

        $process = $this->shell->execInProject('php artisan ui ' . config('lambo.store.frontend'));

        $this->abortIf(! $process->isSuccessful(), "Installation of UI scaffolding did not complete successfully.", $process);

        $this->info('UI scaffolding has been set to ' . config('lambo.store.frontend'));
    }
}
