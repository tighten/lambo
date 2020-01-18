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
        if (config('lambo.store.node') || config('lambo.store.mix')) {
            if(! config('lambo.store.node')){
                app('console')->warn('Installation of NPM dependencies was not specified but is required for asset compilation.');
            }

            app('console')->info('Installing NPM dependencies.');
            $this->shell->execInProject("npm install {$this->extraOptions()}");
        }
    }

    public function extraOptions()
    {
        return config('lambo.store.quiet') ? '--silent' : '';
    }
}
