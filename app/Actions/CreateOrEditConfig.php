<?php

namespace App\Actions;

use Facades\App\Paths;
use Illuminate\Support\Facades\File;

class CreateOrEditConfig
{
    public $configPath;
    public $configFilePath;

    public function __construct()
    {
        $this->configPath = Paths::configDir();
        $this->configFilePath = Paths::configFile();
    }

    public function __invoke()
    {
        $this->ensureConfigFileExists();

        app('console')->info("Opening existing config file: {$this->configFilePath}");
        $this->editConfigFile();
    }

    public function ensureConfigFileExists()
    {
        $this->ensureConfigPathExists();

        // $this->upgradeIniConfigFile(); @todo -- if INI, upgrade to JSON and rename

        if (! File::exists($this->configFilePath)) {
            File::put($this->configFilePath, $this->configFileTemplate());
        }
    }

    public function ensureConfigPathExists()
    {
        if (! File::exists($this->configPath)) {
            File::makeDirectory($this->configPath);
        }
    }

    public function isMac()
    {
        return PHP_OS === 'Darwin';
    }

    public function editor()
    {
        return app('console')->option('editor');
    }

    protected function editConfigFile()
    {
        if (! $this->isMac()) {
            exec("xdg-open {$this->configFilePath}");
            return;
        }

        if ($this->editor()) {
            exec(sprintf('"%s" "%s"',
                $this->editor(),
                $this->configFilePath
            ));
            return;
        }

        exec("open {$this->configFilePath}");
    }

    private function configFileTemplate()
    {
        return json_encode([
            "path" => ".",
            "commit_message" => "Initial commit.",
            "quiet" => false,
            "develop" => false,
            "auth" => false,
            "node" => false,
            "codeeditor" => "",
            "browser" => "",
            "link" => false,
            "secure" => false,
            "frontend" => "vue",
            "create_database" => false,
            "db_username" => "root",
            "db_password" => null,
        ], JSON_PRETTY_PRINT);
    }
}
