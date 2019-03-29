<?php

namespace App\Support;

use LogicException;
use function get_class;
use App\Contracts\VerificationContract;
use Symfony\Component\Process\ExecutableFinder;

abstract class BaseExecutableFinderVerification implements VerificationContract
{
    /**
     * The executable to be found.
     *
     * @var string
     */
    protected $executable;

    /**
     * The finder.
     *
     * @var ExecutableFinder
     */
    protected $finder;

    /**
     * BaseExecutableFinderVerification constructor.
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
        if ($this->finder->find($this->executable) === null) {
            throw new LogicException(ucwords($this->executable) . ' not installed ['. get_class($this) .'].');
        }

        return true;
    }
}
