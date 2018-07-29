<?php

namespace App\Verifications;

use Exception;
use LogicException;
use App\Support\BaseVerification;

class ExamplePasses extends BaseVerification
{
    /**
     * The verification to run the application must implement an 'handle' function that
     * should return a boolean indicating if itself passes of not. Alternatively, an
     * exception may be thrown, giving more context about the error in the message.
     *
     * @throws LogicException
     * @throws Exception
     * @return bool
     */
    public function handle(): bool
    {
        return true;
    }
}
