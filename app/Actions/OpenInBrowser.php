<?php

namespace App\Actions;

use App\Environment;
use App\Shell\Shell;

class OpenInBrowser
{
    use LamboAction;

    protected $shell;
    protected $environment;

    public function __construct(Shell $shell, Environment $environment )
    {
        $this->shell = $shell;
        $this->environment = $environment;
    }

    public function __invoke()
    {
        $this->logStep('Opening in Browser');

        if ($this->environment->isMac() && $this->browser()) {
            $this->shell->execInProject(sprintf(
                'open -a "%s" "%s"',
                $this->browser(),
                config('lambo.store.project_url')
            ));

            return;
        }

        $this->shell->execInProject("valet open");
    }

    public function browser()
    {
        return config('lambo.store.browser');
    }
}
