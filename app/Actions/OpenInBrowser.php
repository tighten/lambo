<?php

namespace App\Actions;

use App\DetectsEnvironment;
use App\Shell\Shell;

class OpenInBrowser
{
    use LamboAction, DetectsEnvironment;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        $this->logStep('Opening in Browser');

        if ($this->isMac() && $this->browser()) {
            $this->shell->execInProject(sprintf(
                'open -a "%s" "%s"',
                $this->browser(),
                config('lambo.store.project_url')
            ));
        } else {
            $this->shell->execInProject("valet open");
        }
    }

    public function browser()
    {
        return config('lambo.store.browser');
    }
}
