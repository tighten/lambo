<?php

namespace App;

use Illuminate\Contracts\Config\Repository;
use Symfony\Component\Process\Process;

class Shell
{
    protected $rootPath;
    protected $projectPath;

    private $useTTY = false;
    private $consoleWriter;

    public function __construct(ConsoleWriter $consoleWriter, Repository $config)
    {
        $this->rootPath = $config->get('lambo.store.root_path');
        $this->projectPath = $config->get('lambo.store.project_path');
        $this->consoleWriter = $consoleWriter;
    }

    public function execInRoot($command)
    {
        return $this->exec("cd {$this->rootPath} && $command");
    }

    public function execInProject($command)
    {
        return $this->exec("cd {$this->projectPath} && $command");
    }

    public function execIn(string $directory, string $command)
    {
        return $this->exec("cd {$directory} && $command");
    }

    public function exec(string $command)
    {
        $process = Process::fromShellCommandline($command)
            ->setTty($this->useTTY)
            ->setTimeout(null)
            ->enableOutput();

        $this->consoleWriter->verbose()->exec($command);

        $process->run(function ($type, $buffer) {
            if (! $this->shouldReportProgress($buffer)) {
                return;
            }

            foreach (explode(PHP_EOL, trim($buffer)) as $line) {
                $this->consoleWriter->consoleOutput($line, $type);
            }
        });

        $this->useTTY = false;
        return $process;
    }

    public function withTTY()
    {
        $this->useTTY = true;
        return $this;
    }

    private function shouldReportProgress($buffer): bool
    {
        if (empty(trim($buffer)) || $buffer === PHP_EOL) {
            return false;
        }

        return $this->consoleWriter->isVerbose() || config('lambo.store.with_output');
    }
}
