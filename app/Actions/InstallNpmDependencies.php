<?php

namespace App\Actions;

use App\Shell\Shell;

class InstallNpmDependencies
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (! config('lambo.store.node')) {
            return;
        }

        $process = $this->shell->execInProject("npm install {$this->extraOptions()}");

        $this->abortIf(! $process->isSuccessful(), 'Installation of npm dependencies did not complete successfully', $process);

        $this->info('Npm dependencies installed.');
    }

    public function extraOptions()
    {
        return config('lambo.store.with-output') ? '' : '--silent';
    }
}
