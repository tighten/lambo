<?php

namespace App\Actions;

use App\Shell\Shell;

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

        $this->logStep('Running valet secure');

        $process = $this->shell->execInProject("valet secure");

        $this->abortIf(! $process->isSuccessful(), 'valet secure did not complete successfully', $process);

        $this->info('valet secure successful');
    }
}
