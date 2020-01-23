<?php

namespace App\Actions;

use App\Shell\Shell;

class InitializeGitRepo
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        $this->logStep('Initializing git repository');

        $this->execAndCheck('git init');
        $this->execAndCheck('git add .');
        $this->execAndCheck('git commit -m "' . config('lambo.store.commit_message') . '"');
        $this->info('New git repository initialized.');
    }

    public function execAndCheck($command)
    {
        $process = $this->shell->execInProject($command);

        $this->abortIf(! $process->isSuccessful(), 'Initialization of git repository did not complete successfully.', $process);
    }
}
