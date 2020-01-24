<?php

namespace App;

trait Environment
{
    public function isMac()
    {
        return PHP_OS === 'Darwin';
    }
}
