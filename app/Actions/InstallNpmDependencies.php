<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class InstallNpmDependencies
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        if (! config('lambo.store.node')) {
            return;
        }

        $this->consoleWriter->logStep('Installing node dependencies');

        $process = $this->shell->execInProject("npm install{$this->withQuiet()}");

        $this->abortIf(! $process->isSuccessful(), 'Installation of npm dependencies did not complete successfully', $process);

        $this->consoleWriter->newLine();
        $this->consoleWriter->verbose()->success('Npm dependencies installed.');
    }

    public function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --silent';
    }
}
