<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class PushToGitHub
{
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

        $branch = config('lambo.store.branch');
        $process = $this->shell->execInProject("git -c credential.helper= -c credential.helper='!gh auth git-credential' push -u origin {$branch}");
        if (! $process->isSuccessful()) {
            $this->consoleWriter->warn('Failed to push project to GitHub.');
            $this->consoleWriter->warn("Failed to run {$process->getCommandLine()}");
            $this->consoleWriter->showOutputErrors($process->getErrorOutput());
            return;
        }

        $this->consoleWriter->success('Successfully pushed project to GitHub');
    }
}
