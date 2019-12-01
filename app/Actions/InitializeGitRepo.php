<?php

namespace App\Actions;

use App\Shell;

class InitializeGitRepo
{
    public function __invoke()
    {
        $shell = new Shell;

        $shell->execInProject('git init');
        $shell->execInProject('git add .');
        $shell->execInProject('git commit -m "Initial commit."');

        app('console')->info('Git repository initialized.');
    }
}
