<?php

namespace App\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class CustomizeDotEnv
{
    public function __invoke()
    {
        $filePath = config('lambo.store.project_path') . '/' . '.env.example';

        $file = collect(explode("\n", File::get($filePath)));

        $file->transform(function ($item) {
            $parts = explode('=', $item, 2);

            // Line doesn't contain an equal sign (=), return same
            if (count($parts) < 2) {
                return $item;
            }

            [$envKey, $envVal] = $parts;

            $replace = $this->value($envKey, $envVal);

            return "{$envKey}={$replace}";
        });

        File::put($filePath, $file->implode("\n"));
        File::put(str_replace('.env.example', 'env', $filePath), $file->implode("\n"));
    }

    public function value($key, $fallback)
    {
        $replacements = [
            'APP_NAME' => config('lambo.store.project_name'),
            'APP_URL' => 'http://' . config('lambo.store.project_url'),
            'DB_DATABASE' => $this->databaseify(config('lambo.store.project_name')),
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => null,
        ];

        return Arr::get($replacements, $key, $fallback);
    }

    public function databaseify($name)
    {
        // @todo
        return $name;
    }
}
