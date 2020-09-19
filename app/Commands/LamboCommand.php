<?php

namespace App\Commands;

use App\ConsoleWriter;
use App\Shell;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class LamboCommand extends Command
{
    protected $consoleWriter;

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->consoleWriter = new ConsoleWriter($input, $output);
        return parent::run($input, $output);
    }

    protected function makeAndInvoke(string $class)
    {
        $this->make($class)();
    }

    protected function make(string $class)
    {
        return app($class, [
            'consoleWriter' => $this->consoleWriter,
            'shell' => app(Shell::class, [
                'consoleWriter' => $this->consoleWriter
            ])
        ]);
    }
}
