<?php

namespace App\Actions;

use App\Shell;

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
        app('console-writer')->logStep('Initializing git repository');

        $this->execAndCheck("git init{$this->withQuiet()}");
        $this->execAndCheck('git add .');
        $this->execAndCheck(sprintf('git commit%s -m "%s"', $this->withQuiet(), config('lambo.store.commit_message')));

        app('console-writer')->success('New git repository initialized.');
    }

    public function execAndCheck($command)
    {
        $process = $this->shell->execInProject($command);

        $this->abortIf(! $process->isSuccessful(), 'Initialization of git repository did not complete successfully.', $process);
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
