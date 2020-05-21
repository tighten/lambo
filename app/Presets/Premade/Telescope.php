<?php

namespace App\Presets\Premade;

use App\Presets\BasePreset;

class Telescope extends BasePreset
{
    public $description = 'Install telescope and stuff';

    // @todo: add documentation about its options

    public function before()
    {
        if (in_array('prod', $this->params)) {
            $this->composerRequires['laravel/telescope'] = '~3.0';
        } else {
            $this->composerDevRequires['laravel/telescope'] = '~3.0';
        }
    }

    public $afterShellCommands = [
        'php artisan telescope:install',
    ];
}
