<?php

namespace App\Shell;


class ColorOutputFormatter
{
    private $colors = ['black', 'red', 'green', 'yellow', 'blue', 'white', 'default'];
    private $started = [];

    public function start(string $message, string $prefix = 'RUN')
    {
        return sprintf("<bg=blue;fg=white> %s </> <fg=blue>%s</>", $prefix, $message);
    }

    public function progress(
        string $buffer,
        bool $error = false,
        string $prefix = 'OUT',
        string $errorPrefix = 'ERR'
    ) {
        if ($error) {
            return rtrim(sprintf("<bg=red;fg=white> %s </> %s", $errorPrefix, $buffer));
        } else {
            return str_replace("\n", sprintf("\n<bg=green;fg=white> %s </> ", $prefix), $buffer);
        }
    }
}
