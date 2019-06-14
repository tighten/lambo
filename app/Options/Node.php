<?php

namespace App\Options;

use App\Support\BaseOption;

class Node extends BaseOption
{
    protected $key = 'node';

    protected $title = 'Node';

    protected $description = 'Whether to run npm install, yarn, or neither';

    protected $values = [
        'NPM' => 'npm',
        'Yarn' => 'yarn',
        'Neither' => false,
    ];
}
