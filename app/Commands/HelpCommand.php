<?php

namespace App\Commands;

use App\Actions\DisplayHelpScreen;
use App\Actions\DisplayLamboWelcome;
use App\Actions\DisplaySpecificHelpScreen;
use LaravelZero\Framework\Commands\Command;

class HelpCommand extends Command
{
    protected $signature = 'help-screen {target?}';
    protected $description = 'Show help';

    public function handle()
    {
        app()->bind('console', function () {
            return $this;
        });

        if (! $this->argument('target')) {
            app(DisplayLamboWelcome::class)();
            app(DisplayHelpScreen::class)();

            return;
        }

        app(DisplaySpecificHelpScreen::class)($this->argument('target'));
    }
}
