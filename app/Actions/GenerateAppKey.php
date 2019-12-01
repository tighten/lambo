<?php

namespace App\Actions;

class GenerateAppKey
{
    public function __invoke()
    {
        (new Shell)->execInProject('php artisan key:generate');
    }
}
