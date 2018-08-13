<?php

namespace App\Actions;

use App\Support\BaseAction;

class InitializeGit extends BaseAction
{
    /**
     * Initializes the Git Repository.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $showOutput = false;

        $gitMessage = config('lambo.message');
        $directory = config('lambo-store.project_path');

        $this->shell->inDirectory($directory, 'git init', $showOutput);
        $this->shell->inDirectory($directory, 'git add .', $showOutput);
        $this->shell->inDirectory($directory, "git commit -m \"{$gitMessage}\"", $showOutput);

        $this->console->info('Git repository initialized.');
    }
}
