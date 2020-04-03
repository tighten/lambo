<?php

namespace App\Presets;

abstract class BasePreset
{
    public function before()
    {

    }

    abstract public function run();

    public function after()
    {

    }
}
