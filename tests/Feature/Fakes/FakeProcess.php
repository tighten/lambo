<?php

namespace Tests\Feature\Fakes;

class FakeProcess
{
    public $isSuccessful;
    public $failedCommand;

    public function __construct(bool $isSuccessful, string $failedCommand = '')
    {
        $this->isSuccessful = $isSuccessful;
        $this->failedCommand = $failedCommand;
    }

    public static function successful()
    {
        return new self(true);
    }

    public static function failed(string $failedCommand)
    {
        return new self(false, $failedCommand);
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
