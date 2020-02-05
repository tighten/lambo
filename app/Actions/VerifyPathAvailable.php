<?php

namespace App\Actions;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class VerifyPathAvailable
{
    use LamboAction;

    public function __invoke()
    {
        $this->logStep('Verifying path availability');

        $rootPath = config('lambo.store.root_path');

        if (! File::isDirectory($rootPath)) {
            throw new Exception($rootPath . ' is not a directory.');
        }

        $projectPath = config('lambo.store.project_path');

        if (File::isDirectory($projectPath)) {
            throw new Exception($projectPath . ' is already a directory.');
        }

        $this->info('Directory ' . $projectPath . ' is available.');
    }
}
