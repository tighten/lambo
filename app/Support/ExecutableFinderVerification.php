<?php

namespace App\Support;

use LogicException;
use function get_class;
use Symfony\Component\Process\ExecutableFinder;

abstract class ExecutableFinderVerification
{
    /**
     * @var string
     */
    protected $executable;

    /**
     * @var ExecutableFinder
     */
    protected $finder;

    /**
     * GitInstalled constructor.
     *
     * @param ExecutableFinder $finder
     */
    public function __construct(ExecutableFinder $finder)
    {
        if ($this->executable === null) {
            throw new LogicException('Please set the executable in ' . get_class($this));
        }

        $this->finder = $finder;
    }

    /**
     * Returns a boolean for the existence of Valet
     *
     * @return bool
     */
    public function handle(): bool
    {
        $find = $this->finder->find($this->executable);

        if ($find === null) {
            throw new LogicException(ucwords($this->executable) . ' not installed ['. get_class($this) .'].');
        }

        return true;
    }
}
