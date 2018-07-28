<?php

namespace App\Actions;

use App\Support\BaseAction;
use App\Services\DisplayService;

class DisplayLamboLogo extends BaseAction
{
    public function __invoke()
    {
        app(DisplayService::class, ['console' => $this->console])->displayLamboLogo();
    }
}
