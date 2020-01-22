<?php

namespace App\Actions;

use App\Shell\Shell;

class OpenInEditor
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if ($this->editor()) {
            $this->logStep('Opening In Editor');

            $this->shell->execInProject($this->editor() . " .");
            $this->info('[ lambo ] Opening your project in ' . $this->editor());
        }
    }

    public function editor()
    {
        return config('lambo.store.editor');
    }
}
