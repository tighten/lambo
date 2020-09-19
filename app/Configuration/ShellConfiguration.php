<?php

namespace App\Configuration;

class ShellConfiguration extends LamboConfiguration
{
    protected function getSettings(): array
    {
        return $_SERVER;
    }
}
