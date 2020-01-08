<?php

namespace App\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class SetConfig
{
    private $savedConfig;

    public function __invoke()
    {
        $configFilePath = sprintf("%s/.lambo/config.json", config('home_dir'));
        $this->savedConfig = File::exists($configFilePath) ? json_decode(File::get($configFilePath), true) : [];

        $tld = $this->getTld();

        config()->set('lambo.store', [
            'tld' => $tld,
            'project_name' => $this->argument('projectName'),
            'root_path' => $this->getBasePath(),
            'project_path' => $this->getBasePath() . '/' . $this->argument('projectName'),
            'project_url' => $this->getProtocol() . $this->argument('projectName') . '.' . $tld,
            'database_username' => $this->getDatabaseUsername(),
            'database_password' => $this->getDatabasePassword(),
        ]);
    }

    private function getTld()
    {
        $home = config('home_dir');

        if (File::exists($home . '/.config/valet/config.json')) {
            return json_decode(File::get($home . '/.config/valet/config.json'))->tld;
        }

        return json_decode(File::get($home . '/.valet/config.json'))->domain;
    }

    private function getBasePath()
    {
        if ($this->option('path')) {
            return str_replace('~', config('home_dir'), $this->option('path'));
        }

        if(Arr::has($this->savedConfig, 'path'))
        {
            return str_replace('~', config('home_dir'), Arr::get($this->savedConfig, 'path'));
        }

        return getcwd();
    }

    private function getProtocol()
    {
        // @todo: If securing, change to https
        return 'http://';
    }

    private function getDatabaseUsername()
    {
        if ($this->option('dbuser')) {
            return $this->option('dbuser');
        }

        if (Arr::exists($this->savedConfig, 'db_username')) {
            return Arr::get($this->savedConfig, 'db_username');
        }

        return 'root';
    }

    private function getDatabasePassword()
    {
        if ($this->option('dbpassword')) {
            return $this->option('dbpassword');
        }

        if (Arr::exists($this->savedConfig, 'db_password')) {
            return Arr::get($this->savedConfig, 'db_password');
        }

        return '';
    }

    private function isMac()
    {
        return PHP_OS === 'Darwin';
    }

    private function argument(string $key) {
        return app('console')->argument($key);
    }

    private function option(string $key)
    {
        return app('console')->option($key);
    }
}
