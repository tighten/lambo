<?php

namespace App\Contracts;

interface OptionContract
{
    /**
     * Changes the key to value in Lambo config
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function setLamboConfig(string $key, $value): void;
}
