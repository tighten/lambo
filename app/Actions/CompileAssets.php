<?php

namespace App\Actions;

use App\Shell;

class CompileAssets
{
    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (config('lambo.store.mix')) {
            app('console')->info('Compiling project assets.');
            $this->shell->execInProject("npm run dev {$this->extraOptions()}");
        }
    }

    public function extraOptions()
    {
        return config('lambo.store.quiet') ? '--silent' : '';
    }
}
