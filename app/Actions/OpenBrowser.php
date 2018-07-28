<?php

namespace App\Actions;

use App\Support\BaseAction;

class OpenBrowser extends BaseAction
{
    public function __invoke()
    {
        $this->shell->inDirectory(config('lambo-store.project_path'), 'valet open');
    }
}
