<?php

namespace App\Options;

use App\Support\BaseOption;

class Browser extends BaseOption
{
    protected $key = 'browser';

    protected $title = 'Browser';

    protected $description = 'The project will open in this browser';

    protected $values = [
        'None' => false,
    ];
}
