<?php

namespace App\Actions;

use App\Shell;

class OpenInEditor
{
    public function __invoke()
    {
        app('console')->info('Opening your editor.');

        if ($this->editor()) {
            (new Shell)->execInProject($this->editor() . " .");
        }
    }

    public function editor()
    {
        return app('console')->option('editor');
    }
}
