<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class PushToGitHub
{
    public const WARNING_FAILED_TO_PUSH = 'Failed to push project to GitHub.';
    public const WARNING_UNABLE_TO_GET_BRANCH_NAME = self::WARNING_FAILED_TO_PUSH . ' Unable to determine git branch name.';

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        if (! (config('lambo.store.push_to_github'))) {
            return;
        }

        $this->consoleWriter->logStep('Pushing new project to GitHub');

        $branchNameProcess = $this->shell->execInProject('git rev-parse --abbrev-ref HEAD');
        if (! $branchNameProcess->isSuccessful()) {
            $this->consoleWriter->warn(self::WARNING_UNABLE_TO_GET_BRANCH_NAME);
            $this->consoleWriter->warn("Failed to run {$branchNameProcess->getCommandLine()}");
            $this->consoleWriter->showOutputErrors($branchNameProcess->getErrorOutput());
            return;
        }

        $process = $this->shell->execInProject("git push -u origin {$branchNameProcess->getOutput()}");
        if (! $process->isSuccessful()) {
            $this->consoleWriter->warn(self::WARNING_FAILED_TO_PUSH);
            $this->consoleWriter->warn("Failed to run {$process->getCommandLine()}");
            $this->consoleWriter->showOutputErrors($process->getErrorOutput());
            return;
        }

        $this->consoleWriter->success('Successfully pushed project to GitHub');
    }
}
