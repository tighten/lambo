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
        if (config('lambo.store.mix') || config('lambo.store.full')) {
            $this->shell->execInProject("npm run dev {$this->extraOptions()}");
            app('console')->info('[ npm ] Project assets compiled successfully.');
        }
    }

    public function extraOptions()
    {
        return config('lambo.store.quiet') ? '--silent' : '';
    }
}
