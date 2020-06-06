<?php

namespace App\Actions;

use App\LamboException;

trait LamboAction
{
    public function abortIf(bool $abort, string $message, $process = null)
    {
        if ($abort) {
            if ($process) {
                throw new LamboException("{$message}\nFailed to run: '{$process->getCommandLine()}'.");
            }
            throw new LamboException("{$message}");
        }
    }
}
