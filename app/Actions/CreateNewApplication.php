<?php

namespace App\Actions;

use App\Support\BaseAction;

class CreateNewApplication extends BaseAction
{
    public function __invoke()
    {
        $dev = config('lambo.dev', false);

        $projectName = $this->console->projectName;

        $directory = config('lambo-store.install_path');

        if ($dev) {
            $this->console->info('Creating application from dev branch.');
            $this->shell->inDirectory($directory, "laravel new {$projectName} --dev");
        } else {
            $this->console->info('Creating application from release branch.');
            $this->shell->inDirectory($directory, "laravel new {$projectName}");
        }
    }
}