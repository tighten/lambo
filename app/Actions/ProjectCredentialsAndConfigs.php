<?php

namespace App\Actions;

use App\Support\BaseAction;

class ProjectCredentialsAndConfigs extends BaseAction
{
    public function __invoke()
    {
        /**
         * Taking care of proper replacements in:
         *
         * .env file
         * config/database.php => default connection mysql/sqlite
         *
         */
        throw new \Exception('Implement me!');
    }
}
