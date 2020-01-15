<?php

namespace App\Actions;

use Facades\App\LamboConfig;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class SetConfig
{
    protected $savedConfig;

    public function __construct()
    {
        $this->savedConfig = $this->loadSavedConfig();
    }

    public function __invoke()
    {
        $tld = $this->getTld();

        config()->set('lambo.store', [
            'tld' => $tld,
            'project_name' => $this->argument('projectName'),
            'root_path' => $this->getBasePath(),
            'project_path' => $this->getBasePath() . '/' . $this->argument('projectName'),
            'project_url' => $this->getProtocol() . $this->argument('projectName') . '.' . $tld,
            'database_username' => $this->getOptionValue('dbuser', 'db_username') ?? 'root',
            'database_password' => $this->getOptionValue('dbpassword', 'db_password') ?? '',
            'commit_message' => $this->getOptionValue('message', 'commit_message') ?? 'Initial commit.',
        ]);
    }

    public function loadSavedConfig()
    {
        $configFilePath = LamboConfig::getFilePath("config.json");

        return File::exists($configFilePath) ? json_decode(File::get($configFilePath), true) : [];
    }

    public function getTld()
    {
        $home = config('home_dir');

        if (File::exists($home . '/.config/valet/config.json')) {
            return json_decode(File::get($home . '/.config/valet/config.json'))->tld;
        }

        return json_decode(File::get($home . '/.valet/config.json'))->domain;
    }

    public function getOptionValue($optionCommandLineName, $optionConfigFileName = null)
    {
        if (is_null($optionConfigFileName)) {
            $optionConfigFileName = $optionCommandLineName;
        }

        if ($this->option($optionCommandLineName)) {
            return $this->option($optionCommandLineName);
        }

        if (Arr::has($this->savedConfig, $optionConfigFileName)) {
            return Arr::get($this->savedConfig, $optionConfigFileName);
        }
    }

    public function getBasePath()
    {
        if ($value = $this->getOptionValue('path')) {
            return str_replace('~', config('home_dir'), $value);
        }

        return getcwd();
    }

    public function getProtocol()
    {
        // @todo: If securing, change to https
        return 'http://';
    }

    public function argument($key)
    {
        return app('console')->argument($key);
    }

    public function option($key)
    {
        return app('console')->option($key);
    }
}
