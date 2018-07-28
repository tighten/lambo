<?php

namespace App\Actions;

use App\Support\BaseAction;
use App\Services\VerificationService;

class RunVerifications extends BaseAction
{
    public function __invoke()
    {
        resolve(VerificationService::class)->handle($this->console);
    }
}
