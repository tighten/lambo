<?php

namespace App\Options;

use App\Support\BaseOption;

class Database extends BaseOption
{
    protected $key = 'database';

    protected $title = 'Database';

    protected $description = 'Create a database with Project Name';

    protected $values = [
        'Yes' => true,
        'No' => false,
    ];
}
