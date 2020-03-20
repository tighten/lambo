<?php

namespace App;

class Utilities
{
    public function prepNameForDatabase($name)
    {
        return str_replace('-', '_', $name);
    }
}
