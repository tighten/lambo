<?php

namespace App\Presets\Premade;

use App\Presets\BasePreset;

class Telescope extends BasePreset
{
    public $description = 'Install telescope and stuff';

    // @todo: add documentation about its options

    public function run()
    {
        if (in_array('prod', $this->params)) {
            $this->composerRequires['laravel/telescope'] = '~1.0';
        } else {
            $this->composerDevRequires['laravel/telescope'] = '~1.0';
        }
    }

    public $afterShellCommands = [
        'php artisan telescope:install',
    ];
}
