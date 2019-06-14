<?php

namespace App\Options;

use App\Support\BaseOption;

class Auth extends BaseOption
{
    protected $key = 'auth';

    protected $title = 'Auth';

    protected $description = 'Whether to run the auth:make command to scaffold authentication routes and views.';

    protected $values = [
        'Yes' => true,
        'No' => false,
    ];
}
