<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class ValetSecure
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
        if (! config('lambo.store.valet_secure')) {
            return;
        }

        $this->consoleWriter->logStep('Running valet secure');

        $process = $this->shell->execInProject("valet secure");
        $this->abortIf(! $process->isSuccessful(), 'valet secure did not complete successfully', $process);

        $this->consoleWriter->success('valet secure successful');
    }
}
