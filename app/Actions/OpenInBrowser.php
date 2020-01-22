<?php

namespace App\Actions;

use App\Shell\Shell;
use Facades\App\Environment;

class OpenInBrowser
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        $this->logStep('Opening in Browser');

        if (Environment::isMac() && $this->browser()) {
            $this->shell->execInProject(sprintf(
                'open -a "%s" "%s"',
                $this->browser(),
                config('lambo.store.project_url')
            ));
            $this->info('[ lambo ] Opened your new site. in ' . $this->browser() . ' Happy coding!');
        } else {
            $this->shell->execInProject("valet open");
        }
    }

    public function browser()
    {
        return config('lambo.store.browser');
    }
}
