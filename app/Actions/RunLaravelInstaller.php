<?php

namespace App\Actions;

use App\Shell;

class RunLaravelInstaller
{
    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        $command = "laravel new " . config('lambo.store.project_name') . $this->extraOptions();
        $branch = config('lambo.store.dev') ? 'develop' : 'release';

        app('console')->info("Creating application from the {$branch} branch.");
        $this->shell->execInRoot($command);
    }

    public function extraOptions()
    {
        return sprintf('%s%s',
            config('lambo.store.dev') ? ' --dev' : '',
            config('lambo.store.quiet') ? ' --quiet' : ''
        );
    }
}
