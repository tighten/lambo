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

    public function exec($command)
    {
        $process = Process::fromShellCommandline($command)
            ->setTty($this->useTTY)
            ->setTimeout(null)
            ->enableOutput();

        app('console-writer')->text($this->start($command));
        $process->run(function ($type, $buffer) {
            if ($this->shouldReportProgress()) {
                if (empty(trim($buffer)) || $buffer === PHP_EOL) {
                    return;
                }

                collect(explode(PHP_EOL, trim($buffer)))
                    ->each(function ($line) use ($type) {
                        app('console-writer')->ignoreVerbosity()->text($this->progress($line, $type));
                    });
                app('console-writer')->newLine();
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

    public function start(string $message)
    {
        return sprintf('%s %s', $this->prefix('EXEC', '<bg=blue;fg=black>'), $message);
    }

    private function shouldReportProgress(): bool
    {
        return app('console-writer')->isVerbose() || config('lambo.store.with_output');
    }

    private function progress(string $line, string $type)
    {
        if ($type === Process::ERR) {
            return sprintf('   %s <fg=yellow>%s</>', $this->prefix('>', '<bg=blue;fg=black>'), $line);
        }
        return sprintf('   %s %s', $this->prefix('>', '<bg=blue;fg=black>'), $line);
    }

    private function prefix(string $prefix, string $format): string
    {
        return app('console')->option('no-ansi')
            ? "[ {$prefix} ]"
            : "{$format} {$prefix} </>";
    }
}
