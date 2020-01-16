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
        $projectName = config('lambo.store.project_name');

        app('console')->info('Creating application using the Laravel installer.');

        $this->shell->execInRoot("laravel new {$projectName} {$this->extraOptions()}");

        // @todo
        // if ($isDev) {
        //     $this->console->info('Creating application from dev branch.');
        //     $this->shell->inDirectory($directory, "laravel new {$projectName} --dev");
        // } else {
            // $this->console->info('Creating application from release branch.');
            // $this->shell->inDirectory($directory, "laravel new {$projectName}");
        // }
    }

    public function extraOptions()
    {
        return config('lambo.store.quiet') ? '--quiet' : '';
    }
}
