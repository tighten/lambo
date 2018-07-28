<?php

namespace App\Support;

use App\Commands\NewCommand;

abstract class BaseAction
{
    /**
     * @var NewCommand
     */
    protected $console;

    /**
     * @var ShellCommand
     */
    protected $shell;

    /**
     * BaseAction constructor.
     *
     * @param NewCommand $console
     * @param ShellCommand $shell
     */
    public function __construct(NewCommand $console, ShellCommand $shell)
    {
        $this->console = $console;

        $this->shell = $shell;
    }
}
