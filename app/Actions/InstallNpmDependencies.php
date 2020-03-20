<?php

namespace App\Actions;

use App\Shell;

class InstallNpmDependencies
{
    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        app('console')->info('Installing NPM dependencies.');

        $this->shell->execInProject("npm install");
    }
}
