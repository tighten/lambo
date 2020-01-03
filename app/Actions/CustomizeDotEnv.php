<?php

namespace App\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class CustomizeDotEnv
{
    public function __invoke()
    {
        $filePath = config('lambo.store.project_path') . '/.env.example';

        $output = $this->customize(File::get($filePath));

        File::put($filePath, $output);
        File::put(str_replace('.env.example', '.env', $filePath), $output);
    }

    public function customize($contents)
    {
        return collect(explode("\n", $contents))->transform(function ($item) {
            $parts = explode('=', $item, 2);

            // Line doesn't contain an equal sign (=); return without modification
            if (count($parts) < 2) {
                return $item;
            }

            [$envKey, $envVal] = $parts;

            $replace = $this->value($envKey, $envVal);

            return "{$envKey}={$replace}";
        })->implode("\n");
    }

    public function value($key, $fallback)
    {
        $replacements = [
            'APP_NAME' => config('lambo.store.project_name'),
            'APP_URL' => config('lambo.store.project_url'),
            'DB_DATABASE' => $this->renameForDatabase(config('lambo.store.project_name')),
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => null,
        ];

        return Arr::get($replacements, $key, $fallback);
    }

    public function renameForDatabase($name)
    {
        return str_replace('-', '_', $name);
    }
}
