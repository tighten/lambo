<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class OptionManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'option.manager';
    }
}
