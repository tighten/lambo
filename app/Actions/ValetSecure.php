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
        if (config('lambo.store.valet_secure')) {
            $this->shell->execInProject("valet secure");
        }
    }
}
