<?php

namespace App\Commands;

use Dotenv\Dotenv;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

trait Debug
{

    protected function arrayToTable(array $data, array $filter = null, string $keyPrefix = '', array $headers = null): void
    {
        if (count($data) === 0) {
            app('console-writer')->text(sprintf('No saved configuration found at "%s".', Config::get('home_dir') . '/.lambo/config'));
            app('console-writer')->newLine();

            return;
        }

        $rows = collect($data)
            ->filter(function ($value, $key) use ($filter) {
                return is_null($filter) ? true : in_array($key, $filter);
            })
            ->map(function ($value, $key) use ($keyPrefix) {
                $type = gettype($value);

                if ($type === 'string') {
                    return empty($value)
                        ? [$keyPrefix . $key, '<bg=magenta;fg=black> "" </>', $type]
                        : [$keyPrefix . $key, '<bg=magenta;fg=black> ' . $value . ' </>', $type];
                }

                if ($type === 'boolean') {
                    return $value
                        ? [$keyPrefix . $key, '<bg=green;fg=black> true </>', 'boolean']
                        : [$keyPrefix . $key, '<bg=red;fg=black> false </>', 'boolean'];
                }

                return [$keyPrefix . $key, $value, $type];
            })->values()->toArray();

        app('console-writer')->table($headers ? $headers : ['key', 'value', 'type'], $rows);
    }

    protected function debugReport(): void
    {
        app('console-writer')->block('Debug', 'Start', 'fg=black;bg=white');

        app('console-writer')->section('Computed configuration');
        app('console-writer')->text([
            'The following is the configuration lambo has computed by merging:',
        ]);
        app('console-writer')->listing([
            'command line parameters',
            'saved configuration',
            'shell environment variables.',
        ]);

        $config = Arr::prepend(Config::get('lambo.store'), Config::get('home_dir'), 'home_dir');
        $this->arrayToTable($config, null, 'lambo.store.', ['Configuration key', 'Value', 'Type']);

        app('console-writer')->section('Pre-flight Configuration');

        app('console-writer')->text('Command line arguments:');
        $this->arrayToTable($this->arguments());

        app('console-writer')->text('Command line options:');
        $this->arrayToTable(
            $this->options(),
            [
                'editor',
                'message',
                'path',
                'browser',
                'frontend',
                'dbname',
                'dbuser',
                'dbpassword',
                'create-db',
                'auth',
                'node',
                'mix',
                'link',
                'secure',
                'quiet',
                'with-output',
                'dev',
                'full',
                'no-editor',
                'projectName',
            ], '--'
        );

        app('console-writer')->text('Saved configuration:');

        $savedConfig = [];
        if (File::isFile(Config::get('home_dir') . '/.lambo/config')) {
            $savedConfig = Dotenv::createMutable(Config::get('home_dir') . '/.lambo', 'config')->load();
        }
        $this->arrayToTable(
            $savedConfig,
            [
                'CODEEDITOR',
                'MESSAGE',
                'PROJECTPATH',
                'BROWSER',
                'FRONTEND',
                'DB_NAME',
                'DB_USERNAME',
                'DB_PASSWORD',
                'CREATE_DATABASE',
                'AUTH',
                'NODE',
                'MIX',
                'LINK',
                'SECURE',
                'QUIET',
                'WITH_OUTPUT',
                'DEVELOP',
                'FULL',
                'NO_EDITOR',
            ]
        );

        app('console-writer')->text('Shell environment variables:');
        $this->arrayToTable(
            $_SERVER,
            [
                'EDITOR'
            ],
            '$'
        );

        app('console-writer')->block('Debug', 'End', 'fg=black;bg=white');
    }
}
