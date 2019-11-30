<?php

namespace App;

use Symfony\Component\Process\Process;

class Shell
{
    public function execInRoot($command)
    {
        return $this->exec($command);
    }

    public function execInProject($command)
    {
        $directory = config('lambo.store.project_name');

        return $this->exec("cd {$directory} && $command");
    }

    protected function exec($command)
    {
        $process = app()->make(Process::class, [
            'command' => $command,
        ]);

        $process->setTimeout(null);

        $process->run(function ($type, $buffer) /*use ($showOutput)*/ {
            echo $buffer;

            // if (Process::ERR === $type) {
                // echo 'ERR > ' . $buffer;
            // } elseif ($showOutput) {
                // echo $buffer;
            // }
        });
    }
}
