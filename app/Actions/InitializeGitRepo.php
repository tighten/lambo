<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class InitializeGitRepo
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep('Initializing git repository');

        $this->exec("git init{$this->withQuiet()}");
        $this->exec('git add .');
        $this->exec(sprintf('git commit%s -m "%s"', $this->withQuiet(), config('lambo.store.commit_message')));

        $this->consoleWriter->success('New git repository initialized.');
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
