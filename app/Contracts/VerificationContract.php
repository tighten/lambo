<?php

namespace App\Contracts;

use Exception;
use Symfony\Component\Console\Exception\LogicException;

interface VerificationContract
{
    /**
     * The verification to run the application must implement a 'handle' function that
     * should return a boolean indicating if itself passes of not. Alternatively, an
     * exception may be thrown, giving more context about the error in the message.
     *
     * @throws LogicException
     * @throws Exception
     * @return bool
     */
    public function handle(): bool;
}
