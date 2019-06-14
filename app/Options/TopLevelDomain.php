<?php

namespace App\Options;

use App\Support\BaseOption;

class TopLevelDomain extends BaseOption
{
    protected $key = 'tld';

    protected $title = 'Top Level Domain';

    protected $description = 'The top level domain for the local server';

    protected $values = [
        '.test' => '.test',
    ];
}
