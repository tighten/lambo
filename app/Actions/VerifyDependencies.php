<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\LamboException;
use Symfony\Component\Process\ExecutableFinder;

class VerifyDependencies
{
    protected $finder;
    private $consoleWriter;
    private $dependencies = [
        'The Laravel installer' => 'laravel|https://laravel.com/docs/installation#installing-laravel',
        'Laravel valet'         => 'valet|https://laravel.com/docs/valet',
        'Git version control'   => 'git|https://git-scm.com/',
    ];

    public function __construct(
        ConsoleWriter $consoleWriter,
        ExecutableFinder $finder

    ) {
        $this->finder = $finder;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep("Verifying dependencies");

        $fail = false;
        collect($this->dependencies)->each(function ($dependency, $description) use (&$fail) {
            list($command, $url) = explode('|', $dependency);
            if (($installedDependency = $this->finder->find($command)) === null) {
                $fail = true;
                $this->consoleWriter->fail("${description} is missing. You can find installation instructions at:");
                $this->consoleWriter->text("       <fg=blue;href={$url}>{$url}</>");
            } else {
                $this->consoleWriter->success("${description} found at:");
                $this->consoleWriter->text("       <fg=blue>{$installedDependency}</>");
            }
        });

        if ($fail) {
            throw new LamboException('Please install missing dependencies and try again.');
        }
    }
}
