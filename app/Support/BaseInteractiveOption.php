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
     * Message for the initial screen.
     *
     * @var string
     */
    protected $message;

    /**
     * Message level for the initial screen.
     *
     * @var string
     */
    protected $messageLevel = 'info';

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

    /**
     * Returns the message for initial screen.
     *
     * @return string
     */
    public function message(): ?string
    {
        return $this->message;
    }

    /**
     * Returns the message level.
     *
     * @return string
     */
    public function messageLevel(): string
    {
        return $this->messageLevel;
    }
}
