<?php

namespace App\Actions;

use App\Support\BaseAction;

class ValetLink extends BaseAction
{
    public function __invoke()
    {
        $directory = config('lambo-store.project_path');

        $valetLink = config('lambo.link');

        if ($valetLink !== false) {
            $this->console->info('Registering Valet Link...');
            $this->shell->inDirectory($directory, 'valet link');
        }
    }
}
