<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class ValetLink
{
    use AbortsCommands;

    protected $shell;
    private $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        if (! config('lambo.store.valet_link')) {
            return;
        }

        $this->consoleWriter->logStep('Running valet link');

        $process = $this->shell->execInProject('valet link');
        $this->abortIf(! $process->isSuccessful(), 'valet link did not complete successfully', $process);

        $this->consoleWriter->success('valet link successful');
    }
}
