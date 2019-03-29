<?php

namespace App\Verifications;

use App\Support\BaseExecutableFinderVerification;

class LaravelInstallerInstalled extends BaseExecutableFinderVerification
{
    /**
     * The executable to be verified.
     *
     * @var string
     */
    protected $executable = 'laravel';
}
