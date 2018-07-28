<?php

namespace App\Verifications;

use App\Support\ExecutableFinderVerification;

class GitInstalled extends ExecutableFinderVerification
{
    /**
     * The executable to be verified.
     *
     * @var string
     */
    protected $executable = 'git';
}
