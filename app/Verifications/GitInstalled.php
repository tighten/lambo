<?php

namespace App\Verifications;

use App\Support\BaseExecutableFinderVerification;

class GitInstalled extends BaseExecutableFinderVerification
{
    /**
     * The executable to be found.
     *
     * @var string
     */
    protected $executable = 'git';
}
