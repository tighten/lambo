<?php

namespace App\Actions;

use App\Options;

class DisplayHelpScreen
{
    protected $indent = 30;

    protected $commands = [
        'help' => 'Display this screen',
        'edit-config' => 'Edit config file',
        'edit-after' => 'Edit "after" file',
    ];

    public function __invoke()
    {
        app('console-writer')->newLine();
        app('console-writer')->text('<comment>Usage:</comment>');
        app('console-writer')->text("  lambo new myApplication [arguments]\n");
        app('console-writer')->text('<comment>Commands (lambo COMMANDNAME):</comment>');

        foreach ($this->commands as $command => $description) {
            $spaces = $this->makeSpaces(strlen($command));
            app('console-writer')->text("  <info>{$command}</info>{$spaces}{$description}");
        }

        app('console-writer')->newLine();
        app('console-writer')->text('<comment>Options (lambo new myApplication OPTIONS):</comment>');

        foreach ((new Options())->all() as $option) {
            app('console-writer')->text($this->createCliStringForOption($option));
        }
    }

    public function createCliStringForOption($option)
    {
        if (isset($option['short'])) {
            $flag = '-' . $option['short'] . ', --' . $option['long'];
        } else {
            $flag = '    --' . $option['long'];
        }

        if (isset($option['param_description'])) {
            $flag .= '=' . $option['param_description'];
        }

        $spaces = $this->makeSpaces(strlen($flag));
        $description = $option['cli_description'];

        return "  <info>{$flag}</info>{$spaces}{$description}";
    }

    public function makeSpaces($count)
    {
        return str_repeat(' ', $this->indent - $count);
    }
}
