<?php

namespace App\Actions;

use Facades\App\LamboConfig;

class EditConfig
{
    public function __invoke()
    {
        LamboConfig::createOrEditFile("config.json", $this->configFileTemplate());
    }

    private function configFileTemplate()
    {
        // $this->upgradeIniConfigFile(); @todo use values in existing ini file if one exists.
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
