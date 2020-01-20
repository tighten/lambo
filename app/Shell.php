<?php

namespace App;

use Illuminate\Config\Repository;
use Symfony\Component\Process\Process;

class Shell
{
    protected $rootPath;
    protected $projectPath;
    protected $hideOutput;

    public function __construct(Repository $config)
    {
        $this->rootPath = $config->get('lambo.store.root_path');
        $this->projectPath = $config->get('lambo.store.project_path');
        $this->hideOutput = $config->get('lambo.store.quiet-shell');
    }

    public function execInRoot($command)
    {
        return $this->exec("cd {$this->rootPath} && $command");
    }

    public function execInProject($command)
    {
        return $this->exec("cd {$this->projectPath} && $command");
    }

    protected function exec($command)
    {
        $process = app()->make(Process::class, [
            'command' => $command,
        ]);

        $process->setTimeout(null);
        $process->disableOutput();

        $hideOutput = $this->hideOutput;
        // @todo resolve this
        $process->run(function ($type, $buffer) use ($hideOutput) {
             if (! $hideOutput) {
                 echo $buffer;
             }
        });
    }
}
