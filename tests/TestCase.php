<?php

namespace Tests;

use LaravelZero\Framework\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function fakeLamboConsole(): void
    {
        app()->bind('console', function () {
            return new class {
                public function comment(){}
                public function info(){}
            };
        });
    }
}
