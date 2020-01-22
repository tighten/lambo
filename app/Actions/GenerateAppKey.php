<?php

namespace App\Actions;

use App\Shell\Shell;

class GenerateAppKey
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        $this->logStep('Running php artisan key:generate');

        $process = $this->shell->execInProject('php artisan key:generate');

        $this->abortIf(! $process->isSuccessful(), 'Failed to generate application key complete successfully', $process);

        $this->info('Application key has been set.');
    }
}
