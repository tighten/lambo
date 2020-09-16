<?php

namespace App\Actions;

use App\Shell;

class ValetLink
{
    use AbortsCommands;

    protected $shell;
    private $consoleWriter;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }


    public function __invoke()
    {
        if (! config('lambo.store.valet_link')) {
            return;
        }

        app('console-writer')->logStep('Running valet link');

        $process = $this->shell->execInProject('valet link');

        $this->abortIf(! $process->isSuccessful(), 'valet link did not complete successfully', $process);

        app('console-writer')->verbose()->success('valet link successful');
    }
}
