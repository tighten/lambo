<?php

namespace App\Options;

use App\Support\BaseOption;

class Path extends BaseOption
{
    protected $key = 'path';

    protected $title = 'Path';

    protected $description = 'The path to install the project to';

    protected $values = [
        'Current Directory' => false,
    ];
}
