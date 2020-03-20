<?php

namespace App;

class Environment
{
    public static function isMac()
    {
        return PHP_OS === 'Darwin';
    }
}
