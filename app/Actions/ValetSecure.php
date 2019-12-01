<?php

namespace App\Actions;

use App\Shell;

class ValetSecure
{
    public function __invoke()
    {
        (new Shell)->execInProject("valet secure");
    }
}
