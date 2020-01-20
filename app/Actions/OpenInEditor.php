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
        if ($this->editor()) {
            $this->shell->execInProject($this->editor() . " .");
            app('console')->info('[ lambo ] Opening your project in ' . $this->editor());
        }
    }

    public function editor()
    {
        return config('lambo.store.editor');
    }
}
