<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class InitializeGitHubRepository
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
        $ghCommandOptions = config('lambo.store.github');
        if (! $ghCommandOptions) {
            return;
        }

        $this->consoleWriter->logStep('Initializing GitHub repository');

        $process = $this->shell->execInProject("gh repo create --confirm {$this->getRepositoryName()} {$ghCommandOptions}");
        $process = $this->shell->execInProject("git -c credential.helper= -c credential.helper='!gh auth git-credential' push -u origin {$this->getBranchName()}");

        $this->consoleWriter->success('GitHub repository initialized');
    }

    protected function getRepositoryName()
    {
        $name = config('lambo.store.project_name');
        $organization = config('lambo.store.github-org');
        return $organization ? "{$organization}/{$name}" : $name;
    }

    protected function getBranchName()
    {
        return config('lambo.store.branch');
    }
}
