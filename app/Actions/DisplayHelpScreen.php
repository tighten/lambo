<?php

namespace App\Actions;

use App\Options;

class DisplayHelpScreen
{
    protected $indent = 30;

    protected $commands = [
        'help-screen' => 'Display this screen',
        'edit-config' => 'Edit config file',
        'edit-after' => 'Edit "after" file',
    ];

    public function __invoke()
    {
        $consoleWriter = app('console-writer');

        $consoleWriter->newLine();
        $consoleWriter->ignoreVerbosity()->text("<comment>Usage:</comment>");
        $consoleWriter->ignoreVerbosity()->text("  lambo new myApplication [arguments]\n");
        $consoleWriter->ignoreVerbosity()->text("<comment>Commands (lambo COMMANDNAME):</comment>");

        foreach ($this->commands as $command => $description) {
            $spaces = $this->makeSpaces(strlen($command));
            $consoleWriter->ignoreVerbosity()->text("  <info>{$command}</info>{$spaces}{$description}");
        }

        $consoleWriter->ignoreVerbosity()->newLine();
        $consoleWriter->ignoreVerbosity()->text("<comment>Options (lambo new myApplication OPTIONS):</comment>");

        foreach ((new Options)->all() as $option) {
            $consoleWriter->ignoreVerbosity()->text($this->createCliStringForOption($option));
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
        return str_repeat(" ", $this->indent - $count);
    }
}
