<?php

namespace App\Actions;

use Exception;

trait LamboAction
{
    protected function line(string $message)
    {
        app('console')->line($message);
    }

    protected function info(string $message)
    {
        app('console')->info($message);
    }

    public function error(string $message)
    {
        app('console')->error($message);
    }

    public function warn(string $message)
    {
        app('console')->warn($message);
    }

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
