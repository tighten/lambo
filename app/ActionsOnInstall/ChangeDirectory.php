<?php

namespace App\ActionsOnInstall;

use App\Support\BaseAction;

class ChangeDirectory extends BaseAction
{
    /**
     * Change the working directory to the project directory.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $changeDirectory = base_path('change-directory.sh');
        $directory = config('lambo.store.project_path');

        shell_exec("$changeDirectory $directory");
    }
}
