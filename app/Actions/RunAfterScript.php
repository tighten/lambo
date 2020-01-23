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
        if ($this->fileExists('after')) {
            $this->logStep('Running after script');

            $process = $this->shell->execInProject("sh " . $this->getFilePath("after"));

            $this->abortIf(! $process->isSuccessful(), 'After file did not complete successfully', $process);

            $this->info('After script has completed.');
        }
    }
}
