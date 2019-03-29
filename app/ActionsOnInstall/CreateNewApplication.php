<?php

namespace App\ActionsOnInstall;

use App\Support\BaseAction;

class CreateNewApplication extends BaseAction
{
    /**
     * Creates the new application.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $isDev = config('lambo.dev', false);

        $projectName = config('lambo.store.project_name');

        $directory = config('lambo.store.install_path');

        if ($isDev) {
            $this->console->info('Creating application from dev branch.');
            $this->shell->inDirectory($directory, "laravel new {$projectName} --dev");
        } else {
            $this->console->info('Creating application from release branch.');
            $this->shell->inDirectory($directory, "laravel new {$projectName}");
        }
    }
}
