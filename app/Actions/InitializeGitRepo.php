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
        $this->shell->execInProject('git add .');
        $this->shell->execInProject('git commit -m "' . $this->gitCommit() . '"');

        app('console')->info('Git repository initialized.');
    }

    public function gitCommit()
    {
        return app('console')->option('message') ?? 'Initial commit.';
    }
}
