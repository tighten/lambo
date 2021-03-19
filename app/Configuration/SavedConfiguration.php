<?php

namespace App\Configuration;

use Dotenv\Dotenv;
use Illuminate\Support\Facades\File;

class SavedConfiguration extends LamboConfiguration
{
    protected function getSettings(): array
    {
        $configurationPath = config('home_dir') . '/' . config('config_dir', '.lambo');
        $configurationFile = config('config_file', 'config');

        if (! File::exists("{$configurationPath}/{$configurationFile}")) {
            return [];
        }

        return Dotenv::createMutable($configurationPath, $configurationFile)->load();
    }
}
