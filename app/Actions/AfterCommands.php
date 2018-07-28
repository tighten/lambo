<?php

namespace App\Actions;

use App\Support\BaseAction;

class AfterCommands extends BaseAction
{
    public function __invoke()
    {
        $directory  = config('lambo-store.project_path');

        $commands   = config('lambo-after.commands');

        foreach ($commands as $command) {
            $this->shell->inDirectory($directory, $command);
        }
    }
}
