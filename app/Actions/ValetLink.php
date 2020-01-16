<?php

namespace App\Actions;

use App\Shell;

class ValetLink
{
    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (config('lambo.store.valet_link')) {
            $this->shell->execInProject("valet link");
        }
    }
}
