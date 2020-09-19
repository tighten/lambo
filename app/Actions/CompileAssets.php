<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class CompileAssets
{
    use AbortsCommands;

    protected $shell;
    protected $silentDevScript;
    protected $consoleWriter;

    public function __construct(Shell $shell, SilentDevScript $silentDevScript, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->silentDevScript = $silentDevScript;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->silentDevScript->add();

        $this->consoleWriter->logStep('Compiling project assets');

        $process = $this->shell->execInProject("npm run dev{$this->extraOptions()}");

        $this->abortIf(! $process->isSuccessful(), 'Compilation of project assets did not complete successfully', $process);

        $this->silentDevScript->remove();

        $this->consoleWriter->verbose()->success('Project assets compiled successfully.');
    }
    public function extraOptions()
    {
        return config('lambo.store.with_output') ? '' : ' --silent';
    }
}
