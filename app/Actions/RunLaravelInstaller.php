<?php

namespace App\Actions;

use App\Shell;

class RunLaravelInstaller
{
    use AbortsCommands;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        app('console-writer')->logStep("Running the Laravel installer");

        $process = $this->shell->execInRoot('laravel new ' . config('lambo.store.project_name') . $this->extraOptions());

        $this->abortIf(! $process->isSuccessful(), "The laravel installer did not complete successfully.", $process);

        app('console-writer')->success($this->getFeedback());
    }

    public function extraOptions()
    {
        return sprintf('%s%s%s',
            config('lambo.store.auth') ? ' --auth' : '',
            config('lambo.store.dev') ? ' --dev' : '',
            config('lambo.store.with_output') ? '' : ' --quiet'
        );
    }

    public function getFeedback(): string
    {
        return sprintf("A new application '%s'%s has been created from the %s branch.",
            config('lambo.store.project_name'),
            config('lambo.store.auth') ? ' with auth scaffolding' : '',
            config('lambo.store.dev') ? 'develop' : 'release'
        );
    }

}
