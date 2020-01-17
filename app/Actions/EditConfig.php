<?php

namespace App\Actions;

use Facades\App\LamboConfig;

class EditConfig
{
    public function __invoke()
    {
        LamboConfig::createOrEditFile("config", $this->configFileTemplate());
    }

    private function configFileTemplate()
    {
        // $this->upgradeIniConfigFile(); @todo use values in existing ini file if one exists.
        return <<<'TEMPLATE'
#!/usr/bin/env bash

PROJECTPATH="."
MESSAGE="Initial commit."
QUIET=false
DEVELOP=false
AUTH=false
NODE=false
CODEEDITOR=""
BROWSER=""
LINK=false
SECURE=false
CREATE_DATABASE=false
DB_USERNAME="root"
DB_PASSWORD=""
TEMPLATE;
    }
}
