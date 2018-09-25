<?php

namespace App\Actions;

use App\Support\BaseAction;

class OpenBrowser extends BaseAction
{
    /**
     * Opens the project in the browser.
     *
     * @return void
     */
    public function __invoke(): void
    {
        if (config('lambo.config.browser') === true) {
            $this->console->info('Opening browser.');
            $this->shell->inDirectory(config('lambo.store.project_path'), 'valet open');
        }
    }
}
