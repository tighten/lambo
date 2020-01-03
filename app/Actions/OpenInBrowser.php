<?php

namespace App\Actions;

use App\Shell;

class OpenInBrowser
{
    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if ($this->isMac() && $this->browser()) {
            $this->shell->exec(sprintf(
                'open -a "%s" "%s"',
                $this->browser(),
                config('lambo.store.project_url')
            ));
        } else {
            $this->shell->execInProject("valet open");
        }
    }

    public function isMac()
    {
        return PHP_OS === 'Darwin';
    }

    public function browser()
    {
        return config('lambo.store.browser');
    }
}
