<?php

namespace App\Actions;

use App\Support\BaseAction;

class AfterCommands extends BaseAction
{
    /**
     * Perform the After Commands in Project Directory.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $directory = config('lambo-store.project_path');

        foreach (config('lambo-after.commands') as $command) {
            $this->shell->inDirectory($directory, $command);
        }
    }
}
