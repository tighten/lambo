<?php

namespace App\Actions;

use App\Shell;

class ValetSecure
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (! config('lambo.store.valet_secure')) {
            return;
        }

        app('console-writer')->logStep('Running valet secure');

        $process = $this->shell->execInProject("valet secure");

        $this->abortIf(! $process->isSuccessful(), 'valet secure did not complete successfully', $process);

        app('console-writer')->success('valet secure successful');
    }
}
