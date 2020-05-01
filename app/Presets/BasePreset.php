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
    protected $params;

    public function __construct(Shell $shell, array $params = [])
    {
        $this->shell = $shell;
        $this->params = $params;
    }

    public function baseBefore()
    {
        foreach ($this->beforeShellCommands as $shellCommand) {
            $this->executeShellCommand($shellCommand);
        }

        $this->before();

        $this->shell->ExecInProject($this->buildComposerRequireString());
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

    public function composerRequires()
    {
        return $this->composerRequires;
    }

    public function composerDevRequires()
    {
        return $this->composerDevRequires;
    }

    public function buildComposerRequireString()
    {
        $requires = [];

        if (! empty($this->composerRequires())) {
            $string = 'composer require ';

            foreach ($this->composerRequires() as $package => $constraint) {
                $string .= sprintf('%s:"%s" ', $package, $constraint);
            }

            $requires[] = rtrim($string);
        }

        if (! empty($this->composerDevRequires())) {
            $string = 'composer require --dev ';

            foreach ($this->composerDevRequires() as $package => $constraint) {
                $string .= sprintf('%s:"%s" ', $package, $constraint);
            }

            $requires[] = rtrim($string);
        }

        return implode(' && ', $requires);
    }
}
