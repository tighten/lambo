<?php

namespace App;

use Illuminate\Config\Repository;
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

    public function exec(string $command)
    {
        $process = Process::fromShellCommandline($command)
            ->setTty($this->useTTY)
            ->setTimeout(null)
            ->enableOutput();

        app('console-writer')->text("{$this->prefix('EXEC', '<bg=blue;fg=black>')} {$command}");

        $process->run(function ($type, $buffer) {
            if (! $this->reportProgress($buffer)) {
                return;
            }

            collect(explode(PHP_EOL, trim($buffer)))->each(function ($line) use ($type) {
                app('console-writer')->ignoreVerbosity()->text($this->formatOutput($type, $line));
            });
        });

        $this->useTTY = false;
        return $process;
    }

    public function withTTY()
    {
        $this->useTTY = true;
        return $this;
    }

    private function reportProgress($buffer): bool
    {
        if (empty(trim($buffer)) || $buffer === PHP_EOL) {
            return false;
        }

        return app('console-writer')->isVerbose() || config('lambo.store.with_output');
    }

    private function formatOutput($type, $line): string
    {
        return ($type === Process::ERR)
            ? "   {$this->prefix('!', '<bg=yellow;fg=black>')} <fg=yellow>{$line}</>"
            : "   {$this->prefix('✓', '<bg=blue;fg=black>')} {$line}";
    }

    private function prefix(string $prefix, string $format): string
    {
        return app('console')->option('no-ansi')
            ? "[ {$prefix} ]"
            : "{$format} {$prefix} </>";
    }
}
