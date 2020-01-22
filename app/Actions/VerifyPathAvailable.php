<?php

namespace App\Actions;

use Exception;
use Illuminate\Filesystem\Filesystem;

class VerifyPathAvailable
{
    use LamboAction;

    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function __invoke()
    {
        $this->logStep('Verifying path availability');

        $rootPath = config('lambo.store.root_path');

        if (! $this->filesystem->isDirectory($rootPath)) {
            throw new Exception($rootPath . ' is not a directory.');
        }

        $projectPath = config('lambo.store.project_path');

        if ($this->filesystem->isDirectory($projectPath)) {
            throw new Exception($projectPath . ' is already a directory.');
        }

        $this->info('Directory ' . $projectPath . ' is available.');
    }
}
