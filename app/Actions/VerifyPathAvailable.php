<?php

namespace App\Actions;

use App\LamboException;
use Illuminate\Support\Facades\File;

class VerifyPathAvailable
{
    use AbortsCommands;

    private $consoleWriter;

    public function __invoke()
    {
        app('console-writer')->logStep('Verifying path availability');

        $rootPath = config('lambo.store.root_path');

        if (! File::isDirectory($rootPath)) {
            throw new LamboException($rootPath . ' is not a directory.');
        }

        $projectPath = config('lambo.store.project_path');

        if (empty($projectPath)) {
            throw new LamboException("Configuration 'lambo.store.project_path' cannot be null or an empty string.");
        }

        if (File::isDirectory($projectPath)) {
            throw new LamboException($projectPath . ' is already a directory.');
        }

        app('console-writer')->success(sprintf('Directory "%s" is available.', config('lambo.store.project_path')));
    }
}
