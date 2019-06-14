<?php

namespace App\Options;

use App\Support\BaseOption;

class Path extends BaseOption
{
    protected $key = 'path';

    protected $title = 'Path';

    protected $description = 'The project will install to this path';

    protected $values = [
        'Current Directory' => false,
    ];
}
