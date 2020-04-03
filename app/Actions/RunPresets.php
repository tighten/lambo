<?php

namespace App\Actions;

use App\InteractsWithLamboConfig;

class RunPresets
{
    use LamboAction, InteractsWithLamboConfig;

    public function __construct()
    {

    }

    public function __invoke()
    {
        dd('presets yay');
    }
}
