<?php

namespace App\Options;

use App\Support\BaseOption;

class ValetLink extends BaseOption
{
    protected $key = 'link';

    protected $title = 'Valet Link';

    protected $description = 'Whether create a Valet link to the project directory';

    protected $values = [
        'Yes' => true,
        'No' => false,
    ];
}
