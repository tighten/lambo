<?php

namespace Tests;

use App\Support\ShellCommand;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $shellCommand;

    public function setup(): void
    {
        parent::setup();
        $this->shellCommand = $this->spy(ShellCommand::class);
    }
}
