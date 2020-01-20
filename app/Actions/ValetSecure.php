<?php

namespace App\Actions;

use App\Shell;

class ValetSecure
{
    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (config('lambo.store.valet_secure') || config('lambo.store.full')) {
            app('console')->info('[ valet ] securing new project');
            $this->shell->execInProject("valet secure");
        }
    }
}
