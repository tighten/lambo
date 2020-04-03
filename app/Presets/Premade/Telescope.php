<?php

namespace App\Presets\Premade;

use App\Presets\BasePreset;

class Telescope extends BasePreset
{
    public $composerRequires = [
        'laravel/telescope' => '~1.0',
    ];

    public $afterShellCommands = [
        'php artisan telescope:install',
    ];
}
