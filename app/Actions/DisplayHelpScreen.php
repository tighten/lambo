<?php

namespace App\Actions;

use App\Options;
use Illuminate\Support\Arr;

class DisplayHelpScreen
{
    protected $indent = 30;

    protected $commands = [
        'help-screen' => 'Display this screen',
        'make-config' => 'Generate config file',
        'edit-config' => 'Edit config file',
        'make-after' => 'Generate "after" file',
        'edit-after' => 'Edit "after" file',
    ];

    public function __invoke()
    {
        $console = app('console');

        $console->line("\n<comment>Usage:</comment>");
        $console->line("  lambo new myApplication [arguments]\n");
        $console->line("<comment>Commands (lambo COMMANDNAME):</comment>");

        foreach ($this->commands as $command => $description) {
            $spaces = $this->makeSpaces(strlen($command));
            $console->line("  <info>{$command}</info>{$spaces}{$description}");
        }

        $console->line("\n<comment>Options (lambo new myApplication OPTIONS):</comment>");

        foreach ((new Options)->all() as $option) {
            $console->line($this->createCliStringForOption($option));
        }
    }

    public function createCliStringForOption($option)
    {
        $flag = '--' . $option['long'];

        if (isset($option['short'])) {
            $flag = '-' . $option['short'] . ', ' . $flag;
        }

        $flag .= Arr::get($option, 'param_description', '');

        $spaces = $this->makeSpaces(strlen($flag));
        $description = $option['cli_description'];

        return "  <info>{$flag}</info>{$spaces}{$description}";
    }

    public function makeSpaces($count)
    {
        return collect(range(1, $this->indent - $count))->map(function () {
            return ' ';
        })->implode('');
    }
}
