<?php

namespace App\Options;

use App\Support\BaseOption;

class CommitMessage extends BaseOption
{
    protected $key = 'commit';

    protected $title = 'Commit Message';

    protected $description = 'Commit message for the initial commit';

    protected $values = [
        'None' => false,
    ];
}
