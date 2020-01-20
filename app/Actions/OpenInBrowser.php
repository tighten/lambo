<?php

namespace App\Actions;

use App\Shell;
use Facades\App\Environment;

class OpenInBrowser
{
    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (Environment::isMac() && $this->browser()) {
            $this->shell->execInProject(sprintf(
                'open -a "%s" "%s"',
                $this->browser(),
                config('lambo.store.project_url')
            ));
            app('console')->info('[ lambo ] Opened your new site. in ' . $this->browser() . ' Happy coding!');
        } else {
            $this->shell->execInProject("valet open");
        }
        app('console')->info('[ lambo ] Opened your new site. Happy coding!');
    }

    public function browser()
    {
        return config('lambo.store.browser');
    }
}
