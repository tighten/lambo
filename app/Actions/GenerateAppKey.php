<?php

namespace App\Actions;

use App\Shell;

class GenerateAppKey
{
    public function __invoke()
    {
        (new Shell)->execInProject('php artisan key:generate');
    }
}
