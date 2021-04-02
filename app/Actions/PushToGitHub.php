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
        $branch = config('lambo.store.branch');
        $process = $this->shell->execInProject("git -c credential.helper= -c credential.helper='!gh auth git-credential' push -u origin {$branch}");
        if (! $process->isSuccessful()) {
            $this->consoleWriter->warn('Failed to push project to GitHub');
            return;
        }

        $this->consoleWriter->success('Successfully pushed project to GitHub');
    }
}
