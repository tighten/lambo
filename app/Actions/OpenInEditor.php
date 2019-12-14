<?php

namespace App\Actions;

use App\Shell;

class OpenInEditor
{
    public function __invoke()
    {
        app('console')->info('Opening your editor.');

        (new Shell)->execInProject($this->editor() . " .");
    }

    public function editor()
    {
        // For when we allow it to be customized
        return 'code';
    }
}
