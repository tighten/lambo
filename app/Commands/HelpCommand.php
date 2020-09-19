<?php

namespace App\Commands;

use App\Actions\DisplayHelpScreen;
use App\Actions\DisplayLamboWelcome;

class HelpCommand extends LamboCommand
{
    protected $signature = 'help-screen';
    protected $description = 'Show help';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->makeAndInvoke(DisplayLamboWelcome::class);
        $this->makeAndInvoke(DisplayHelpScreen::class);
    }
}
