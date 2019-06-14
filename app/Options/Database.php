<?php

namespace App\Options;

use App\Support\BaseOption;

class Database extends BaseOption
{
    protected $key = 'database';

    protected $title = 'Database';

    protected $description = 'The database type to create with the project name';

    protected $values = [
        'MySQL' => 'mysql',
        'Sqlite' => 'sqlite',
        'Neither' => false,
    ];
}
