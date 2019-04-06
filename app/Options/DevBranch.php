<?php

namespace App\Options;

use App\Support\BaseOption;

class DevBranch extends BaseOption
{
    public function getKey(): string
    {
        return 'dev';
    }

    public function displayDescription(): string
    {
        return 'Use development branch?';
    }

    public function displayValue(): string
    {
        return $this->value ? 'Yes' : 'No';
    }
}
