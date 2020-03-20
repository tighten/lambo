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
        $this->shell->execInProject("valet secure");
    }
}
