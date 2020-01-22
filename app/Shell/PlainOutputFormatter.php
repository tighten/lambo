<?php

namespace App\Shell;


class PlainOutputFormatter
{
    public function start(string $message, string $prefix = 'RUN')
    {
        return sprintf("[ %s ] %s", $prefix, $message);
    }

    public function progress(
        string $buffer,
        bool $error = false,
        string $prefix = 'OUT',
        string $errorPrefix = 'ERR'
    ) {
        if ($error) {
            return rtrim(sprintf("[ %s ] %s", $errorPrefix, $buffer));
        } else {
            return rtrim(sprintf("[ %s ] %s", $prefix, $buffer));
        }
    }
}
