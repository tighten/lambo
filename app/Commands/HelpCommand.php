<?php

namespace App\Commands;

use App\Actions\DisplayHelpScreen;
use App\Actions\DisplayLamboWelcome;

class HelpCommand extends LamboCommand
{
    protected $signature = 'help-screen';
    protected $description = 'Show help';

    public function handle()
    {
        app(DisplayLamboWelcome::class)();
        app(DisplayHelpScreen::class)();
    }
}
