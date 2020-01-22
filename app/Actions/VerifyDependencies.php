<?php

namespace App\Actions;

use Exception;
use Symfony\Component\Process\ExecutableFinder;

class VerifyDependencies
{
    use LamboAction;

    protected $finder;

    protected $dependencies = [
        'laravel',
        'git',
        'valet',
    ];

    public function __construct(ExecutableFinder $finder)
    {
        $this->finder = $finder;
    }

    public function __invoke()
    {
        $this->logStep('Verifying dependencies');
        foreach ($this->dependencies as $dependency) {
            if ($this->finder->find($dependency) === null) {
                throw new Exception($dependency . ' not installed');
            }
        }
        $this->info('Dependencies: laravel (installer), git and valet are available.');
    }
}
