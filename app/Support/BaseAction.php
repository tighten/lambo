<?php

namespace App\Support;

use LaravelZero\Framework\Commands\Command;

abstract class BaseAction
{
    /**
     * @var Command
     */
    protected $console;

    /**
     * @var ShellCommand
     */
    protected $shell;

    /**
     * BaseAction constructor.
     *
     * @param Command $console
     * @param ShellCommand $shell
     */
    public function __construct(Command $console, ShellCommand $shell)
    {
        $this->console = $console;

        $this->shell = $shell;
    }
}
