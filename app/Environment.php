<?php

namespace App;

class Environment
{
    public function isMac()
    {
        return PHP_OS === 'Darwin';
    }
}
