<?php

namespace App\Actions;

use App\Shell;

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
        app('console-writer')->logStep('Setting APP_KEY in .env');

        $process = $this->shell->execInProject(sprintf("php artisan key:generate%s", $this->withQuiet()));

        $this->abortIf(! $process->isSuccessful(), 'Failed to generate application key successfully', $process);

        app('console-writer')->success('APP_KEY has been set.');
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
