<?php

namespace Tests\Feature\Fakes;

class FakeProcess
{
    public $isSuccessful;
    public $failedCommand;

    public static function success()
    {
        return new self(true);
    }

    public static function fail(string $failedCommand)
    {
        return new self(false, $failedCommand);
    }

    public function __construct(bool $isSuccessful, string $failedCommand = '')
    {
        $this->isSuccessful = $isSuccessful;
        $this->failedCommand = $failedCommand;
    }

    public function isSuccessful()
    {
        return $this->isSuccessful;
    }

    public function getCommandLine()
    {
        return $this->failedCommand;
    }
}
