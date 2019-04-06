<?php

namespace App\Support;

use App\Contracts\OptionContract;

abstract class BaseOption implements OptionContract
{
    protected $value;

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }
}
