<?php

namespace App\Actions;

use App\Commands\Debug;
use App\ConsoleWriter;
use App\Shell;
use Symfony\Component\Process\ExecutableFinder;

class ValidateConfiguration
{
    use Debug;

    protected $consoleWriter;
    protected $shell;
    protected $finder;

    public function __construct(ExecutableFinder $finder, Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->consoleWriter = $consoleWriter;
        $this->shell = $shell;
        $this->finder = $finder;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep('Validating configuration');

        config(['lambo.store.frontend' => $this->getFrontendConfiguration()]);
        $this->checkTeamsConfiguration();

        $this->checkGithubConfiguration();

        $this->consoleWriter->success('Configuration is valid.');

        if ($this->consoleWriter->isDebug()) {
            $this->debugReport();
        }
    }

    protected function debugReport(): void
    {
        $this->consoleWriter->panel('Debug', 'Start', 'fg=black;bg=white');

        $this->consoleWriter->text([
            'Configuration may have changed after validation',
            'Configuration is now as follows:',
        ]);
        $this->configToTable();

        $this->consoleWriter->panel('Debug', 'End', 'fg=black;bg=white');
    }

    private function getFrontendConfiguration(): string
    {
        $inertia = config('lambo.store.inertia');
        $livewire = config('lambo.store.livewire');

        if ($inertia && $livewire) {
            return $this->chooseBetweenFrontends();
        }

        if (! $inertia && ! $livewire) {
            return 'none';
        }

        return $inertia ? 'inertia' : 'livewire';
    }

    private function chooseBetweenFrontends()
    {
        $this->consoleWriter->warn('inertia and livewire cannot be used together. ');

        $options = [
            'use inertia' => 'inertia',
            'use livewire' => 'livewire',
            'continue without frontend scaffolding' => 'none',
        ];
        $choice = app('console')->choice('What would you like to do?', array_keys($options), 2);

        $this->consoleWriter->ok($choice);

        return $options[$choice];
    }

    private function checkTeamsConfiguration()
    {
        if ((config('lambo.store.frontend') === 'none') && config('lambo.store.teams')) {
            $this->consoleWriter->note('You specified --teams but neither inertia or livewire are being used. Skipping...');
        }
    }

    private function checkGithubConfiguration()
    {
        $githubConfiguration = config('lambo.store.github');
        if (! $githubConfiguration) {
            return;
        }

        $ghInstalled = $this->finder->find('gh');
        $this->warnWithExplanationIf(
            ! $ghInstalled,
            'Lambo is unable to create a new GitHub repository',
            "Lambo uses the official GitHub command line tool to create new repositories but you don't seem to have it installed.",
            'https://github.com/cli/cli#installation',
        );

        $authenticatedWithGitHub = $this->shell->execQuietly(['gh', 'auth', 'status'])->isSuccessful();
        $this->warnWithExplanationIf(
            $ghInstalled && ! $authenticatedWithGitHub,
            'Lambo is unable to create a new GitHub repository',
            'You are not logged into Github. Please run <comment>gh auth login</comment>.',
            'https://cli.github.com/manual/gh_auth_login',
        );

        if (! $ghInstalled || ! $authenticatedWithGitHub) {
            config(['lambo.store.github' => false]);
            $choice = app('console')->confirm('Would you like Lambo to continue without GitHub repository creation?');
            if (! $choice) {
                exit;
            }
        }
    }

    private function warnWithExplanationIf(bool $shouldWarn, string $warning, string $explanation, string $url = ''): void
    {
        if ($shouldWarn) {
            $this->consoleWriter->warn($warning);
            $this->consoleWriter->text([
                $explanation,
                $url ? "For more information visit, {$url}." : '',
            ]);
        }
    }
}
