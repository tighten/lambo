<?php

namespace App\Actions;

class DisplayHelpScreen
{
    public function __invoke()
    {
        app('console')->info('help screen soon');
        dd('ded');
    }
}
