<?php

namespace App\Commands;

use App\Actions\EditConfig as EditConfigAction;
use LaravelZero\Framework\Commands\Command;

class EditConfig extends Command
{
    protected $signature = 'edit-config {--editor= : Open the config file in the specified <info>EDITOR</info> or the system default if none is specified.}';

    protected $description = 'Edit Config File. A new config file is created if one does not already exist.';

    public function handle()
    {
        app()->bind('console', function () {
            return $this;
        });

        app(EditConfigAction::class)();
    }
}
