<?php

namespace Tests;

use App\ConsoleWriter;
use App\Shell;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $shell;
    protected $consoleWriter;

    function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->consoleWriter = $this->mock(ConsoleWriter::class);
        $this->consoleWriter->shouldReceive('logstep');
        $this->consoleWriter->shouldReceive('success');
        $this->consoleWriter->shouldReceive('text');
        $this->consoleWriter->shouldReceive('warn');
        $this->consoleWriter->shouldReceive('fail');
        $this->consoleWriter->shouldReceive('newLine');
        $this->consoleWriter->shouldReceive('verbose')->andReturnSelf();

        $this->shell = $this->mock(Shell::class);

        app()->bind('console', function () {
            return new class {
                public function comment($message = '') {}
                public function info() {}
                public function warn($message = '') {}
                public function choice(string $choice) {}
            };
        });

        app()->bind('console-writer', function () {
            return new class() {
                public function ok($message): void {}
                public function fail($message): void {}
                public function logStep($message, $verbosity = null) {}
                public function listing(array $items): void {}
                public function table(array $columnHeadings, array $rowData) {}
                public function alert(string $text){}
                public function isDebug(): bool {}
                public function __call($name, $arguments) {}
            };
        });
    }
}
