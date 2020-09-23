<?php

namespace App\Actions;

use App\Shell;

class InitializeGitRepo
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        app('console-writer')->logStep('Initializing git repository');

        $this->exec("git init{$this->withQuiet()}");
        $this->exec('git add .');
        $this->exec(sprintf('git commit%s -m "%s"', $this->withQuiet(), config('lambo.store.commit_message')));

        app('console-writer')->verbose()->success('New git repository initialized.');
    }

    public function exec($command)
    {
        $process = $this->shell->execInProject($command);
        $this->abortIf(! $process->isSuccessful(), 'Initialization of git repository did not complete successfully.', $process);
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
