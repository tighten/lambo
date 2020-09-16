<?php

namespace App\Actions;

use App\Environment;
use App\Shell;

class OpenInBrowser
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }


    public function __invoke()
    {
        if (config('lambo.store.no_browser')) {
            return;
        }

        app('console-writer')->logStep('Opening in Browser');

        if (Environment::isMac() && $this->browser()) {
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
