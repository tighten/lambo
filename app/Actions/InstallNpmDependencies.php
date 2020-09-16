<?php

namespace App\Actions;

use App\Shell;

class InstallNpmDependencies
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }


    public function __invoke()
    {
        app('console-writer')->logStep('Installing node dependencies');

        $process = $this->shell->execInProject("npm install{$this->withQuiet()}");

        $this->abortIf(! $process->isSuccessful(), 'Installation of npm dependencies did not complete successfully', $process);

        app('console-writer')->newLine();
        app('console-writer')->verbose()->success('Npm dependencies installed.');
    }

    public function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --silent';
    }
}
