<?php

namespace App\Actions;

use App\Shell;

class OpenInEditor
{
    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        app('console')->info('Opening your editor.');

        if ($this->editor()) {
            $this->shell->execInProject($this->editor() . " .");
        }

        // @todo: should we default to $EDITOR (environment var)
    }

    public function editor()
    {
        return app('console')->option('editor');
    }
}
