<?php

namespace App\Commands;

use App\ConsoleWriter;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class LamboCommand extends Command
{
    public function run(InputInterface $input, OutputInterface $output)
    {
        app()->singleton('console-writer', function () use ($input, $output) {
            return  new ConsoleWriter($input, $output);
        });

        return parent::run($input, $output);
    }
}
