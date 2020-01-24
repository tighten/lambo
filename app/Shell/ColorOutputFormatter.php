<?php

namespace App\Shell;


class ColorOutputFormatter extends ConsoleOutputFormatter
{
    public function getStartMessageFormat(): string
    {
        return "<bg=blue;fg=white> RUN </> <fg=blue>%s</>";
    }

    public function getErrorMessageFormat(): string
    {
        return "<bg=red;fg=white> ERR </> %s";
    }

    public function getMessageFormat(): string
    {
        return "<bg=green;fg=white> OUT </> %s";
    }
}
