<?php

namespace App\Support;

use Symfony\Component\Process\Process;

class ShellCommand
{
    /**
     * Run a command in the given directory, relative to current working dir
     *
     * @param string $directory
     * @param string $command
     * @param bool $showOutput
     */
    public function inDirectory(string $directory, string $command, bool $showOutput = true): void
    {
        $this->inCurrentWorkingDir("cd {$directory} && $command", $showOutput);
    }

    /**
     * Run a command in the current working dir
     *
     * @param string $command
     * @param bool $showOutput
     */
    public function inCurrentWorkingDir(string $command, bool $showOutput = true): void
    {
        $execute = app()->make(Process::class, [
            'commandline' => $command,
            'cwd' => null,
            'env' => null,
            'timeout' => null]  // <-- The important one, not to timeout (say: composer install or yarn run dev
        );

        $execute->run(function ($type, $buffer) use ($showOutput) {
            if (Process::ERR === $type) {
                echo 'ERR > ' . $buffer;
            } elseif ($showOutput) {
                echo $buffer;
            }
        });
    }
}
