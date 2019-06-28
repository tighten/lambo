<?php

namespace App\ActionsOnInstall;

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
        $this->console->info('Opening browser.');

        if (config('lambo.config.browser') === true) {
            $this->shell->inDirectory(config('lambo.store.project_path'), 'valet open');
        } else {
            $this->shell->inCurrentWorkingDir('open ' . escapeshellarg(config('lambo.store.project_url')) . ' -a' . str_replace(' ', '\ ', config('lambo.config.browser')));
        }
    }
}
