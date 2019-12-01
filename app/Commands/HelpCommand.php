<?php

namespace App\Commands;

use App\Actions\DisplayHelpScreen;
use App\Actions\DisplayLamboWelcome;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class HelpCommand extends Command
{
    protected $signature = 'lambo:help';
    protected $description = 'Show help';

    public function handle()
    {
        app()->bind('console', function () {
            return $this;
        });

        app(DisplayLamboWelcome::class)();
        app(DisplayHelpScreen::class)();
    }
}
