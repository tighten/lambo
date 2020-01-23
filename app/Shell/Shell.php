<?php

namespace App\Shell;

use Illuminate\Config\Repository;
use Symfony\Component\Process\Process;

class Shell
{
    protected $rootPath;
    protected $projectPath;

    public function __construct(Repository $config)
    {
        $this->rootPath = $config->get('lambo.store.root_path');
        $this->projectPath = $config->get('lambo.store.project_path');
    }

    public function execInRoot($command)
    {
        return $this->exec("cd {$this->rootPath} && $command", $command);
    }

    public function execInProject($command, $description = '')
    {
        return $this->exec("cd {$this->projectPath} && $command", $command);
    }

    protected function exec($command, $description)
    {
        $out = app('Symfony\Component\Console\Output\ConsoleOutput');
        $debugFormatter = app('console')->option('no-ansi')
            ? new PlainOutputFormatter
            : new ColorOutputFormatter;

        $process = app()->make(Process::class, [
            'command' => $command,
        ]);
        $process->setTimeout(null);

        $out->writeln($debugFormatter->start(
            $description
        ));

        $withOutput = config('lambo.store.with_output');
        $process->run(function ($type, $buffer) use ($out, $debugFormatter, $process, $withOutput) {

            if (empty($buffer) || $buffer === PHP_EOL) {
                return;
            }

            if (Process::ERR === $type || $withOutput) {
                $out->writeln(
                    $debugFormatter->progress(
                        $buffer,
                        Process::ERR === $type
                    )
                );
            }
        });

        return $process;
    }
}
