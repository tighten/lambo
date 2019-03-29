<?php

namespace App\ActionsOnInstall;

use App\Support\BaseAction;

class ValetLink extends BaseAction
{
    /**
     * Performs the Valet Link command.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $directory = config('lambo.store.project_path');

        $valetLink = config('lambo.config.link');

        if ($valetLink !== false) {
            $this->console->info('Registering Valet Link...');
            $this->shell->inDirectory($directory, 'valet link');
        }
    }
}
