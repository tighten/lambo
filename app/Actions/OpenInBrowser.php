<?php

namespace App\Actions;

use App\Shell;

class OpenInBrowser
{
    public function __invoke()
    {
        (new Shell)->execInProject("valet open");
    }
}
