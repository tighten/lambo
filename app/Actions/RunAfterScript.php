<?php

namespace App\Actions;

use App\Shell\Shell;
use Facades\App\LamboConfig;

class RunAfterScript
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (LamboConfig::fileExists('after')) {
            $this->logStep('Running after script');

            $process = $this->shell->execInProject("sh " . LamboConfig::getFilePath("after"));

            $this->abortIf(! $process->isSuccessful(), 'After file did not complete successfully', $process);

            $this->info('After script has completed.');
        }
    }
}
