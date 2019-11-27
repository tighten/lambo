<?php

namespace App\Commands;

use App\Actions\VerifyDependencies;
use LaravelZero\Framework\Commands\Command;

class NewCommand extends Command
{
    protected $signature = 'new
        {projectName : Name of the Laravel project}
    ';

    protected $description = 'Creates a fresh Laravel application';

    public function handle()
    {
        app(VerifyDependencies::class)();
    }
}
