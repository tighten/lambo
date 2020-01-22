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

        $this->shell->execInProject('git init');
        $this->shell->execInProject('git add .');
        $this->shell->execInProject('git commit -m "' . config('lambo.store.commit_message') . '"');
        $this->info('New git repository initialized.');
    }

}
