<?php

namespace App\Actions;

use Symfony\Component\Process\ExecutableFinder;

class VerifyDependencies
{
    use AbortsCommands;

    protected $finder;

    private $dependencies = [
        [
            'command' => 'laravel',
            'label' => 'The Laravel Installer',
            'instructions_url' => 'https://laravel.com/docs/installation#installing-laravel',
        ],
        [
            'command' => 'valet',
            'label' => 'Laravel valet',
            'instructions_url' => 'https://laravel.com/docs/valet',
        ],
        [
            'command' => 'git',
            'label' => 'Git version control',
            'instructions_url' => 'https://git-scm.com',
        ],
    ];

    public function __construct(ExecutableFinder $finder)
    {
        $this->finder = $finder;
    }

    public function __invoke()
    {
        app('console-writer')->logStep("Verifying dependencies");

        $this->abortIf(
            collect($this->dependencies)->reduce(function ($carry, $dependency) {
                list($command, $label, $instructionsUrl) = array_values($dependency);
                if (($installedDependency = $this->finder->find($command)) === null) {
                    app('console-writer')->warn("{$label} is missing. You can find installation instructions at:\n        <fg=blue;href={$instructionsUrl}>{$instructionsUrl}</>");
                    return true;
                }
                app('console-writer')->success("{$label} found at:\n        <fg=blue>{$installedDependency}</>");
                return $carry ?? false;
            }),
            'Please install missing dependencies and try again.');
    }
}
