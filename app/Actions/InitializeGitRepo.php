<?php

namespace App\Actions;

use App\Shell;

class InitializeGitRepo
{
    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        $this->shell->execInProject('git init');
        app('console')->info('[ git ] initialized new repository.');

        $this->shell->execInProject('git add .');
        app('console')->info('[ git ] staged files for commit.');

        $this->shell->execInProject('git commit -m "' . config('lambo.store.commit_message') . '"');
        app('console')->info('[ git ] committed new project.');
    }
}
