<?php

namespace App\Presets;

use App\Shell\Shell;

abstract class BasePreset
{
    public $composerRequires = [];
    public $beforeShellCommands = [];
    public $afterShellCommands = [];
    // public $presetDependencies = []; // @todo later

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function before()
    {
        foreach ($this->beforeShellCommands as $shellCommand) {
            $this->executeShellCommand($shellCommand);
        }

        $this->shell->ExecInProject($this->buildComposerRequireString());
    }

    public function run()
    {
        // Do nothing by default
    }

    public function after()
    {
        foreach ($this->afterShellCommands as $shellCommand) {
            $this->executeShellCommand($shellCommand);
        }
    }

    public function executeShellCommand($shellCommand)
    {
        $this->shell->execInProject($shellCommand);
    }

    public function buildComposerRequireString()
    {
        $string = 'composer require ';

        foreach ($this->composerRequires as $package => $constraint) {
            $string .= sprintf('%s:"%s" ', $package, $constraint);
        }

        return rtrim($string);
    }
}
