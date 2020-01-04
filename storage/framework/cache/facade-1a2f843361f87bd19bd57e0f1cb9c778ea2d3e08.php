<?php

namespace Facades\App;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Utilities
 */
class Utilities extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'App\Utilities';
    }
}
