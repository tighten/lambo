<?php

namespace App\Actions;

use App\Shell;

class OpenEditor
{
    public function __invoke()
    {
        app('console')->info('Opening your editor.');

        (new Shell)->execInProject("code .");
    }
}
