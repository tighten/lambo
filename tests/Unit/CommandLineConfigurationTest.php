<?php

namespace Tests\Unit;

use App\Configuration\CommandLineConfiguration;
use Illuminate\Support\Arr;
use LaravelZero\Framework\Commands\Command;
use Tests\TestCase;

class CommandLineConfigurationTest extends TestCase
{
    private $mockConsole;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockConsole = $this->mock(Command::class);
        $this->withCommandLineArgument('projectName', 'foo');
    }

    /** @test */
    function it_gets_a_command_line_configuration_value()
    {
        $this->withoutCommandLineArguments();

        // lambo --command-line-option=foo
        $this->withCommandLineOptions(['command-line-option' => 'foo']);

        $this->swap('console', $this->mockConsole);

        $commandLineConfiguration = new CommandLineConfiguration([
            'command-line-option' => 'genericOption',
        ]);

        $this->assertEquals('foo', $commandLineConfiguration->genericOption);
    }

    /** @test */
    function it_returns_null_if_a_command_line_configuration_value_is_missing()
    {
        $this->withoutCommandLineArguments();
        $this->withoutCommandLineOptions();

        $this->swap('console', $this->mockConsole);

        $commandLineConfiguration = new CommandLineConfiguration([
            'command-line-option' => 'genericOption',
        ]);

        $this->assertNull($commandLineConfiguration->genericOption);
    }

    /** @test */
    function it_returns_null_if_a_command_line_configuration_value_is_empty()
    {
        $this->withoutCommandLineArguments();

        // lambo --command-line-option=
        $this->withCommandLineOptions(['command-line-option' => '']);

        $this->swap('console', $this->mockConsole);

        $commandLineConfiguration = new CommandLineConfiguration([
            'command-line-option' => 'genericOption',
        ]);

        $this->assertNull($commandLineConfiguration->genericOption);
    }

    /** @test */
    function it_returns_null_if_a_non_existent_property_is_requested()
    {
        $this->withoutCommandLineArguments();
        $this->withoutCommandLineOptions();

        $this->swap('console', $this->mockConsole);

        $commandLineConfiguration = new CommandLineConfiguration([]);
        $this->assertNull($commandLineConfiguration->foo);
    }

    protected function withoutEnvironmentVariable(array $keys): void
    {
        Arr::forget($_SERVER, $keys);
    }

    protected function withoutCommandLineOptions(): void
    {
        $this->withCommandLineOptions([]);
    }

    protected function withCommandLineOptions(array $commandLineOptions): void
    {
        $this->mockConsole->shouldReceive('options')
            ->andReturn($commandLineOptions);
    }

    protected function withCommandLineArgument(string $key, string $value): void
    {
        $this->mockConsole->shouldReceive('argument')
            ->with($key)
            ->andReturn($value);
    }

    private function withoutCommandLineArguments()
    {
        $this->mockConsole->shouldReceive('arguments')->andReturn([]);
    }
}
