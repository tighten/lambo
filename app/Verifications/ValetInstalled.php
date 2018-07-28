<?php

namespace App\Verifications;

use App\Support\ExecutableFinderVerification;

class ValetInstalled extends ExecutableFinderVerification
{
    /**
     * The executable to be verified.
     *
     * @var string
     */
    protected $executable = 'valet';
}
