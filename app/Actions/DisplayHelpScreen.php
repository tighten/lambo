<?php

namespace App\Actions;

use App\Options;

class DisplayHelpScreen
{
    protected $indent = 30;

    protected $commands = [
        'help' => 'Display this screen',
        'edit-config' => 'Edit "~/.lambo/config" file',
        'edit-after' => 'Edit "~/.lambo/after" file',
        'new' => 'Scaffold a new Laravel application',
    ];

    public function __invoke()
    {
        app('console-writer')->newLine();

        app('console-writer')->text('<comment>Commands (lambo COMMANDNAME):</comment>');
        foreach ($this->commands as $command => $description) {
            $spaces = $this->makeSpaces(strlen($command));
            app('console-writer')->text("  <info>{$command}</info>{$spaces}{$description}");
        }

        app('console-writer')->newLine();
        app('console-writer')->text('<comment>Usage:</comment>');
        app('console-writer')->text('  lambo help');
        app('console-writer')->text('  lambo [common options] edit-config [--editor=<editor>]');
        app('console-writer')->text('  lambo [common options] edit-after [--editor=<editor>]');
        app('console-writer')->text('  lambo [common options] new myApplication [--editor=<editor>] [options]');

        app('console-writer')->newLine();
        app('console-writer')->text('<comment>Options (lambo new myApplication):</comment>');
        foreach ((new Options())->all() as $option) {
            app('console-writer')->text($this->createCliStringForOption($option));
        }

        app('console-writer')->newLine();
        app('console-writer')->text('<comment>Common options:</comment>');
        foreach ((new Options())->common() as $option) {
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
