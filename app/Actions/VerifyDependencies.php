<?php

namespace App\Actions;

use Exception;
use Symfony\Component\Process\ExecutableFinder;

class VerifyDependencies
{
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

        foreach ($this->dependencies as $dependency) {
            if ($this->finder->find($dependency) === null) {
                throw new Exception($dependency . ' not installed');
            }
        }
        app('console')->info('[ lambo ] dependencies laravel installer, git and valet are available.');
    }
}
