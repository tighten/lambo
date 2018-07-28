<?php

namespace App\Actions;

use App\Support\BaseAction;

class MakeAuth extends BaseAction
{
    public function __invoke()
    {
        $directory = config('lambo-store.project_path');

        $makeAuth = config('lambo.auth');

        if ($makeAuth !== false) {
            $this->console->info('Scaffolding authentication...');
            $this->shell->inDirectory($directory, 'php artisan make:auth');
        }
    }
}
