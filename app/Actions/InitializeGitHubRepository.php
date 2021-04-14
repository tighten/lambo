<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class InitializeGitHubRepository
{
    use AbortsCommands;

    public const WARNING_FAILED_TO_CREATE_REPOSITORY = 'Failed to create new GitHub repository';

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
        if (! $process->isSuccessful()) {
            $this->consoleWriter->warn(self::WARNING_FAILED_TO_CREATE_REPOSITORY);
            $this->consoleWriter->warnCommandFailed($process->getCommandLine());
            $this->consoleWriter->showOutputErrors($process->getErrorOutput());
            return;
        }

        config(['lambo.store.push_to_github' => true]);
        $this->consoleWriter->success('Successfully created new GitHub repository');
    }

    protected function getRepositoryName()
    {
        $name = config('lambo.store.project_name');
        $organization = config('lambo.store.github-org');

        return $organization ? "{$organization}/{$name}" : $name;
    }
}
