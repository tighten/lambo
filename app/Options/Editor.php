<?php

namespace App\Options;

use App\Support\BaseOption;

class Editor extends BaseOption
{
    protected $key = 'editor';

    protected $title = 'Editor';

    protected $description = 'The project will open in this editor';

    protected $values = [
        'Sublime' => 'subl',
    ];
}
