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
        if (! config('lambo.store.initialize_github')) {
            return;
        }

        $this->consoleWriter->logStep('Initializing GitHub repository');

        $process = $this->shell->execInProject(sprintf(
            'gh repo create %s --confirm %s%s%s%s%s%s',
            $this->getRepositoryName(),
            config('lambo.store.github.visibility') ?: '--private',
            config('lambo.store.github.no-issues') ? ' --enable-issues=false' : '',
            config('lambo.store.github.no-wiki') ? ' --enable-wiki=false' : '',
            $this->getOption('--description', config('lambo.store.github.description')),
            $this->getOption('--homepage', config('lambo.store.github.homepage')),
            $this->getOption('--team', config('lambo.store.github.team')),
        ));

        $process = $this->shell->execInProject(
            "git -c credential.helper= -c credential.helper='!gh auth git-credential' push -u origin " . config('lambo.store.branch')
        );

        $this->consoleWriter->success('GitHub repository initialized');
    }

    protected function getRepositoryName()
    {
        $name = config('lambo.store.project_name');
        $organization = config('lambo.store.github.organization');
        return $organization ? "{$organization}/{$name}" : $name;
    }

    private function getOption(string $optionKey, ?string $optionValue): string
    {
        return $optionValue ? " {$optionKey}='{$optionValue}'" : '';
    }
}
