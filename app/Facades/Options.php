<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Options extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'options';
    }
}
