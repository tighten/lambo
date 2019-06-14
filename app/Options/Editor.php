<?php

namespace App\Options;

use App\Support\BaseOption;

class Editor extends BaseOption
{
    protected $key = 'editor';

    protected $title = 'Editor';

    protected $description = 'The editor to open the project in';

    protected $values = [
        'None' => false,
        'Sublime' => 'subl',
    ];
}
