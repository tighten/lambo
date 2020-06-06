<?php

namespace App\Actions;

use App\LamboException;
use Symfony\Component\Process\ExecutableFinder;

class VerifyDependencies
{
    protected $finder;

    public function __construct(ExecutableFinder $finder)
    {
        $this->finder = $finder;
    }

    public function __invoke(array $dependencies)
    {
        app('console-writer')->logStep("Verifying dependencies");

        $fail = false;
        collect($dependencies)->each(function ($dependency, $description) use (&$fail) {
            list($command, $url) = explode('|', $dependency);
            if (($installedDependency = $this->finder->find($command)) === null) {
                $fail = true;
                app('console-writer')->fail("${description} is missing. You can find installation instructions at:");
                app('console-writer')->text("       <fg=blue;href={$url}>{$url}</>");
            } else {
                app('console-writer')->success("${description} found at:");
                app('console-writer')->text("       <fg=blue>{$installedDependency}</>");
            }
        });

        if ($fail) {
            throw new LamboException('Please install missing dependencies and try again.');
        }
    }
}
