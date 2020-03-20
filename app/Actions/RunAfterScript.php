<?php

namespace App\Actions;

use App\InteractsWithLamboConfig;
use App\Shell\Shell;

class RunAfterScript
{
    use LamboAction, InteractsWithLamboConfig;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (! $this->configFileExists('after')) {
            return;
        }

        $this->logStep('Running after script');

        $process = $this->shell->execInProject("sh " . $this->getConfigFilePath("after"));

        $this->abortIf(! $process->isSuccessful(), 'After file did not complete successfully', $process);

        $this->info('After script has completed.');
    }
}
