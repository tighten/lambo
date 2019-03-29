<?php

namespace App\Verifications;

use App\Support\BaseExecutableFinderVerification;

class ValetInstalled extends BaseExecutableFinderVerification
{
    /**
     * The executable to be verified.
     *
     * @var string
     */
    protected $executable = 'valet';
}
