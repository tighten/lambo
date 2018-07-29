<?php

namespace App\Contracts;

interface InteractiveOptionContract
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
