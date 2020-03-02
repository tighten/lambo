<?php

namespace App\Actions;

use Exception;
use Symfony\Component\Process\ExecutableFinder;

class VerifyDependencies
{
    use LamboAction;

    protected $finder;

    public function __construct(ExecutableFinder $finder)
    {
        $this->finder = $finder;
    }

    public function __invoke(array $dependencies)
    {
        $this->logStep('Verifying dependencies');
        foreach ($dependencies as $dependency) {
            if ($this->finder->find($dependency) === null) {
                throw new Exception($dependency . ' not installed');
            }
        }
        $this->info('Dependencies: ' . implode(', ', $dependencies) . ' are available.');
    }
}
