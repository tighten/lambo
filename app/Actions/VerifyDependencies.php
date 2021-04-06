<?php

namespace App\Actions;

use App\ConsoleWriter;
use Symfony\Component\Process\ExecutableFinder;

class VerifyDependencies
{
    use AbortsCommands;

    protected $finder;
    protected $consoleWriter;

    private $dependencies = [
        [
            'command' => 'composer',
            'label' => 'Composer',
            'instructions_url' => 'https://getcomposer.org',
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

    public function __construct(ExecutableFinder $finder, ConsoleWriter $consoleWriter)
    {
        $this->finder = $finder;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep('Verifying dependencies');

        $this->abortIf(
            collect($this->dependencies)->reduce(function ($carry, $dependency) {
                list($command, $label, $instructionsUrl) = array_values($dependency);
                if (($installedDependency = $this->finder->find($command)) === null) {
                    $this->consoleWriter->warn("{$label} is missing. You can find installation instructions at:\n        <fg=blue;href={$instructionsUrl}>{$instructionsUrl}</>");
                    return true;
                }
                $this->consoleWriter->success("{$label} found at:\n        <fg=blue>{$installedDependency}</>");
                return $carry ?? false;
            }),
            'Please install missing dependencies and try again.'
        );
    }
}
