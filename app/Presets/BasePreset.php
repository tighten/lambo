<?php

namespace App\Presets;

use App\Shell\Shell;

abstract class BasePreset
{
    public $description = '';
    public $composerRequires = [];
    public $composerDevRequires = [];
    public $beforeShellCommands = [];
    public $afterShellCommands = [];
    // public $presetDependencies = []; // @todo later

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function baseBefore()
    {
        foreach ($this->beforeShellCommands as $shellCommand) {
            $this->executeShellCommand($shellCommand);
        }

        $this->shell->ExecInProject($this->buildComposerRequireString());

        $this->before();
    }

    public function before()
    {
        // Do nothing
    }

    public function baseRun()
    {
        $this->run();
    }

    public function run()
    {
        // Do nothing by default
    }

    public function baseAfter()
    {
        foreach ($this->afterShellCommands as $shellCommand) {
            $this->executeShellCommand($shellCommand);
        }

        $this->after();
    }

    public function after()
    {
        // Do nothing by default
    }

    public function executeShellCommand($shellCommand)
    {
        $this->shell->execInProject($shellCommand);
    }

    public function buildComposerRequireString()
    {
        $requires = [];

        if (! empty($this->composerRequires)) {
            $string = 'composer require ';

            foreach ($this->composerRequires as $package => $constraint) {
                $string .= sprintf('%s:"%s" ', $package, $constraint);
            }

            $requires[] = rtrim($string);
        }

        if (! empty($this->composerDevRequires)) {
            $string = 'composer require --dev ';

            foreach ($this->composerDevRequires as $package => $constraint) {
                $string .= sprintf('%s:"%s" ', $package, $constraint);
            }

            $requires[] = rtrim($string);
        }

        return implode(' && ', $requires);
    }
}
