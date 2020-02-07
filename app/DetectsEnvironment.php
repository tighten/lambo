<?php

namespace App;

trait DetectsEnvironment
{
    public function isMac()
    {
        return PHP_OS === 'Darwin';
    }
}
