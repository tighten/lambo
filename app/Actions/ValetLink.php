<?php

namespace App\Actions;

use App\Shell\Shell;

class ValetLink
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (config('lambo.store.valet_link') || config('lambo.store.full')) {
            $this->logStep('Running valet link');

            $process = $this->shell->execInProject("valet link");

            $this->abortIf(! $process->isSuccessful(), 'valet link did not complete successfully', $process);

            $this->info('valet link successful');
        }
    }
}
