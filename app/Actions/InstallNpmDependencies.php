<?php

namespace App\Actions;

use App\Shell;

class InstallNpmDependencies
{
    public function __invoke()
    {
        app('console')->info('Installing NPM dependencies.');

        (new Shell)->execInProject("npm install");
    }
}
