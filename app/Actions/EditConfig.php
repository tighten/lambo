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
        return <<<'TEMPLATE'
PROJECTPATH="."
MESSAGE="Initial commit."
QUIET=false
DEVELOP=false
AUTH=false
FRONTEND=
NODE=false
MIX=false
CODEEDITOR=""
BROWSER=""
LINK=false
SECURE=false
CREATE_DATABASE=false
DB_NAME=
DB_USERNAME=root
DB_PASSWORD=
TEMPLATE;
    }
}
