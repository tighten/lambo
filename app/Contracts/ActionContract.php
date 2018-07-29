<?php

namespace App\Contracts;

interface ActionContract
{
    /**
     * This must perform the action that the class name indicates. The class
     * is also responsible to determine if it should be performed or not.
     *
     * @return void
     */
    public function __invoke(): void;
}
