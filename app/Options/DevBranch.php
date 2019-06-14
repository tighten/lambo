<?php

namespace App\Options;

use App\Support\BaseOption;

class DevBranch extends BaseOption
{
    protected $key = 'dev';

    protected $title = 'Dev Branch';

    protected $description = 'Whether to use the Laravel development branch';

    protected $values = [
        'No' => false,
        'Yes' => true,
    ];
}
