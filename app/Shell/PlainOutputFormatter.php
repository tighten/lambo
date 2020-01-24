<?php

namespace App\Shell;

class PlainOutputFormatter extends ConsoleOutputFormatter
{
    public function getStartMessageFormat(): string
    {
        return "[ RUN ] %s";
    }

    public function getErrorMessageFormat(): string
    {
        return "[ ERR ] %s";
    }

    public function getMessageFormat(): string
    {
        return "[ OUT ] %s";
    }
}
