<?php

namespace App;

use Illuminate\Contracts\Config\Repository;
use Symfony\Component\Process\Process;

class Shell
{
    protected $rootPath;
    protected $projectPath;

    private $useTTY = false;

    public function __construct(Repository $config)
    {
        $this->rootPath = $config->get('lambo.store.root_path');
        $this->projectPath = $config->get('lambo.store.project_path');
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

    public function execQuietly(string $command, bool $quiet = false) {
        return $this->exec($command, true);
    }

    public function exec(string $command, bool $quiet = false)
    {
        $process = Process::fromShellCommandline($command)
            ->setTty($this->useTTY)
            ->setTimeout(null)
            ->enableOutput();

        if (! $quiet) {
            app('console-writer')->verbose()->exec($command);
        }

        $process->run(function ($type, $buffer) use ($quiet) {
            if (! $this->shouldReportProgress($buffer) || $quiet) {
                return;
            }

            foreach (explode(PHP_EOL, trim($buffer)) as $line) {
                app('console-writer')->consoleOutput($line, $type);
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

        return app('console-writer')->isVerbose() || config('lambo.store.with_output');
    }
}
