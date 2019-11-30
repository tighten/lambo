<?php

namespace App\Actions;

use App\Shell;

class RunLaravelInstaller
{
    public function __invoke()
    {
        $projectName = config('lambo.store.project_name');

        app('console')->info('Creating application using the Laravel installer.');

        (new Shell)->execInRoot("laravel new {$projectName}");

        // @todo
        // if ($isDev) {
        //     $this->console->info('Creating application from dev branch.');
        //     $this->shell->inDirectory($directory, "laravel new {$projectName} --dev");
        // } else {
            // $this->console->info('Creating application from release branch.');
            // $this->shell->inDirectory($directory, "laravel new {$projectName}");
        // }
    }
}
