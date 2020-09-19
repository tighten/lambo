<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Options;

class DisplayHelpScreen
{
    protected $indent = 30;

    protected $commands = [
        'help-screen' => 'Display this screen',
        'edit-config' => 'Edit config file',
        'edit-after' => 'Edit "after" file',
    ];

    private $consoleWriter;

    public function __construct(ConsoleWriter $consoleWriter)
    {
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->consoleWriter->newLine();
        $this->consoleWriter->text("<comment>Usage:</comment>");
        $this->consoleWriter->text("  lambo new myApplication [arguments]\n");
        $this->consoleWriter->text("<comment>Commands (lambo COMMANDNAME):</comment>");

        foreach ($this->commands as $command => $description) {
            $spaces = $this->makeSpaces(strlen($command));
            $this->consoleWriter->text("  <info>{$command}</info>{$spaces}{$description}");
        }

        $this->consoleWriter->newLine();
        $this->consoleWriter->text("<comment>Options (lambo new myApplication OPTIONS):</comment>");

        foreach ((new Options)->all() as $option) {
            $this->consoleWriter->text($this->createCliStringForOption($option));
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
