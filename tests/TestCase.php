<?php

namespace Tests;

use App\ConsoleWriter;
use App\Shell;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Tests\Feature\Fakes\FakeProcess;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $shell;

    function setUp(): void
    {
        parent::setUp();

        $this->mockConsoleWriter();

        $this->shell = $this->mock(Shell::class);
    }

    protected function mockConsoleWriter(): void
    {
        $consoleWriter = $this->mock(ConsoleWriter::class, function ($consoleWriter) {
            $consoleWriter->shouldReceive('logStep');
            $consoleWriter->shouldReceive('title');
            $consoleWriter->shouldReceive('success');
            $consoleWriter->shouldReceive('note');
            $consoleWriter->shouldReceive('text');
            $consoleWriter->shouldReceive('warn');
            $consoleWriter->shouldReceive('fail');
            $consoleWriter->shouldReceive('newLine');
        });

        $this->swap('console-writer', $consoleWriter);
        $this->swap(ConsoleWriter::class, $consoleWriter);
    }

    protected function todo(array $lines)
    {
        $this->skipWithMessage($lines, 'TODO');
    }

    protected function toSTDOUT($out, string $title = null): void
    {
        $message = sprintf("%s%s\n", $title ? "{$title}\n" : '', print_r($out, true));
        fwrite(STDOUT, $message);
    }

    protected function shouldExecInProject(string $command, bool $success = true)
    {
        $shell = $this->shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->globally()
            ->ordered();

        $success
            ? $shell->andReturn(FakeProcess::success())
            : $shell->andReturn(FakeProcess::fail($command));
    }

    protected function shouldExecInProjectAndFail(string $command)
    {
        $this->shouldExecInProject($command, false);
    }

    protected function isVerbose(): bool
    {
        return $this->isDebug() || in_array('--verbose', $_SERVER['argv'], true);
    }

    protected function isDebug(): bool
    {
        return in_array('--debug', $_SERVER['argv'], true);
    }
}
