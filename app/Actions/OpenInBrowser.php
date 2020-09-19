<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Environment;
use App\Shell;

class OpenInBrowser
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        if (config('lambo.store.no_browser')) {
            return;
        }

        $this->consoleWriter->logStep('Opening in Browser');

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
