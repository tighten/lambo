<?php

namespace App\Actions;

use App\Shell\Shell;

class RunLaravelInstaller
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        $this->logStep('Running the Laravel installer');

        $process = $this->shell->execInRoot('laravel new ' . config('lambo.store.project_name') . $this->extraOptions());

        $this->abortIf(! $process->isSuccessful(), "The laravel installer did not complete successfully.", $process);

        if ($process->isSuccessful()) {
            $this->info($this->getFeedback());
            return;
        }
    }

    public function extraOptions()
    {
        return sprintf('%s%s%s',
            config('lambo.store.dev') ? ' --dev' : '',
            $this->withAuth() ? ' --auth' : '',
            $this->withCommandOutput() ? '' : ' --quiet'
        );
    }

    protected function withAuth(): bool
    {
        return config('lambo.store.auth') || config('lambo.store.full');
    }

    protected function getFeedback(): string
    {
        return sprintf("A new application '%s'%s has been created from the %s branch.",
            config('lambo.store.project_name'),
            $this->withAuth() ? ' with auth scaffolding' : '',
            config('lambo.store.dev') ? 'develop' : 'release'
        );
    }

}
