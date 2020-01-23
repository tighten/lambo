<?php

namespace App\Actions;

use Exception;
use Symfony\Component\Process\Process;

trait LamboAction
{
    public function logStep($step)
    {
        app('console')->comment("\n{$step}...");
    }

    protected function info(string $message)
    {
        app('console')->info($message);
    }

    public function error(string $message)
    {
        app('console')->error($message);
    }

    public function reportError(Process $process)
    {
        $this->error("Failed to run '{$process->getCommandLine()}'");
    }

    public function warn(string $message)
    {
        app('console')->warn($message);
    }

    public function abortIf(bool $abort, string $message, $process)
    {
        if ($abort) {
            throw new Exception("{$message}\n  Failed to run: '{$process->getCommandLine()}'");
        }
    }
}
