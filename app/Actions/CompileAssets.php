<?php

namespace App\Actions;

use App\Shell;

class CompileAssets
{
    use AbortsCommands;

    protected $shell;
    protected $silentDevScript;

    public function __construct(Shell $shell, SilentDevScript $silentDevScript)
    {
        $this->shell = $shell;
        $this->silentDevScript = $silentDevScript;
    }

    public function __invoke()
    {
        if (! config('lambo.store.mix')) {
            return;
        }

        $this->silentDevScript->add();

        app('console-writer')->logStep('Compiling project assets');

        $process = $this->shell->execInProject("npm run dev{$this->extraOptions()}");

        $this->abortIf(! $process->isSuccessful(), 'Compilation of project assets did not complete successfully', $process);

        $this->silentDevScript->remove();

        app('console-writer')->success('Project assets compiled successfully.');
    }
    public function extraOptions()
    {
        return config('lambo.store.with_output') ? '' : ' --silent';
    }
}
