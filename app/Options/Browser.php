<?php

namespace App\Options;

use App\Support\BaseOption;

class Browser extends BaseOption
{
    protected $key = 'browser';

    protected $title = 'Browser';

    protected $description = 'The browser to open the project in';

    protected $values = [
        'None' => false,
        'System Default' => true,
    ];
}
