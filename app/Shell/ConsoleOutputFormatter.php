<?php

namespace App\Shell;

abstract class ConsoleOutputFormatter
{
    public function start(string $message)
    {
        return sprintf($this->getStartMessageFormat(), $message);
    }

    public function progress(string $buffer, bool $error) {

        if ($error) {
            return rtrim(sprintf($this->getErrorMessageFormat(), $buffer));
        }

        return rtrim(sprintf($this->getMessageFormat(), $buffer));
    }

    abstract function getStartMessageFormat(): string;

    abstract function getErrorMessageFormat(): string;

    abstract function getMessageFormat(): string;
}