<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;
use Symfony\Component\Process\ExecutableFinder;

class ValidateGithubConfiguration
{
    public const WARNING_UNABLE_TO_CREATE_REPOSITORY = 'Unable to create a new GitHub repository';
    public const INSTRUCTIONS_GH_NOT_INSTALLED = [
        "Lambo uses the official GitHub command line tool to create new repositories but it doesn't seem to be installed.",
        'For installation instructions, please visit <fg=blue;options=underscore>https://github.com/cli/cli#installation</>',
    ];
    public const INSTRUCTIONS_GH_NOT_AUTHENTICATED = [
        'You are not logged into Github. Please run <comment>gh auth login</comment>.',
        'For more information, please visit, <fg=blue;options=underscore>https://cli.github.com/manual/gh_auth_login</>',
    ];
    public const QUESTION_SHOULD_CONTINUE = 'Would you like Lambo to continue without GitHub repository creation?';

    private $consoleWriter;
    private $shell;
    private $finder;

    public function __construct(ExecutableFinder $finder, Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->consoleWriter = $consoleWriter;
        $this->shell = $shell;
        $this->finder = $finder;
    }

    public function __invoke()
    {
        if (! config('lambo.store.github')) {
            return;
        }

        $ghInstalled = $this->finder->find('gh');
        if ($ghInstalled) {
            $authenticatedWithGitHub = $this->shell->execQuietly('gh auth status')->isSuccessful();

            if (! $authenticatedWithGitHub) {
                $this->consoleWriter->warn(self::WARNING_UNABLE_TO_CREATE_REPOSITORY);
                $this->consoleWriter->text(self::INSTRUCTIONS_GH_NOT_AUTHENTICATED);
            }
        } else {
            $this->consoleWriter->warn(self::WARNING_UNABLE_TO_CREATE_REPOSITORY);
            $this->consoleWriter->text(self::INSTRUCTIONS_GH_NOT_INSTALLED);
        }

        if (! $ghInstalled || ! $authenticatedWithGitHub) {
            config(['lambo.store.github' => false]);
            if (! app('console')->confirm(self::QUESTION_SHOULD_CONTINUE)) {
                exit;
            }
        }
    }
}
