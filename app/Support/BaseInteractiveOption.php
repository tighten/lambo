<?php

namespace App\Support;

use App\Contracts\InteractiveOptionContract;
use Symfony\Component\Process\ExecutableFinder;

abstract class BaseInteractiveOption implements InteractiveOptionContract
{
    /**
     * Option value.
     *
     * @var string
     */
    protected $value;

    /**
     * The finder.
     *
     * @var ExecutableFinder
     */
    protected $finder;

    /**
     * BaseInteractiveOption constructor.
     *
     * @param ExecutableFinder $finder
     */
    public function __construct(ExecutableFinder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Option key
     *
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * Option value
     *
     * @return string
     */
    public function value(): ?string
    {
        return $this->value;
    }
}
