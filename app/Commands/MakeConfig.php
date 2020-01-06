<?php

namespace App\Commands;

use App\Actions\CreateOrEditConfig;
use LaravelZero\Framework\Commands\Command;

class MakeConfig extends Command
{
    protected $signature = 'make-config {--editor= : Open the config file in the specified <info>EDITOR</info> or the system default if none is specified.}';

    protected $description = 'Make Config File';

    public function handle()
    {
        app()->bind('console', function () {
            return $this;
        });
        app(CreateOrEditConfig::class)();
    }
}
