<?php

namespace App\Services;

use App\Support\ShellCommand;

class AfterCommandsService
{
    /**
     * @var ShellCommand
     */
    private $shellCommand;

    public function __construct(ShellCommand $shellCommand)
    {
        $this->shellCommand = $shellCommand;
    }

    public function handle()
    {
        $commands = config('lambo.after.commands', []);

        foreach ($commands as $command)
        {
            $this->shellCommand->inCurrentWorkingDir($command);
        }
    }

}