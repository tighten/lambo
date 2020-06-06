<?php

namespace App\Configuration;

use Dotenv\Dotenv;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class   SavedConfiguration extends LamboConfiguration
{
    protected function getSettings(): array
    {
        $configurationPath = Config::get('home_dir') . '/' . Config::get('config_dir', '.lambo');
        $configurationFile = Config::get('config_file', 'config');

        if (! File::exists("{$configurationPath}/{$configurationFile}")) {
            return [];
        }

        return Dotenv::createMutable($configurationPath, $configurationFile)->load();
    }
}
