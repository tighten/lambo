<?php

namespace App\Actions;

use App\Shell;
use Facades\App\LamboConfig;

class RunAfterScript
{
    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (LamboConfig::fileExists('after')) {
            $this->shell->execInProject("sh " . LamboConfig::getFilePath("after"));
        }
    }
}
