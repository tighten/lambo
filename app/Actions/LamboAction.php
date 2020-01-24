<?php

namespace App\Actions;

use App\LogsToConsole;
use Exception;

trait LamboAction
{
    use LogsToConsole;

    public function logStep($step)
    {
        app('console')->comment("\n{$step}...");
    }

    public function abortIf(bool $abort, string $message, $process)
    {
        if ($abort) {
            throw new Exception("{$message}\n  Failed to run: '{$process->getCommandLine()}'");
        }
    }
}
