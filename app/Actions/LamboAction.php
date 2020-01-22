<?php

namespace App\Actions;

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

    public function withCommandOutput(): bool
    {
        return config('lambo.store.with_output');
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

    public function abort(string $message, Process $process)
    {
        app('console')->error("\n[ lambo ] {$message}\nFailed to run: '{$process->getCommandLine()}'");
        exit(1);
    }

    public function abortIf(bool $abort, string $message, $process)
    {
        if ($abort) {
            $this->abort($message, $process);
        }
    }
}
