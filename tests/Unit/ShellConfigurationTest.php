<?php

namespace Tests\Unit;

use App\Configuration\ShellConfiguration;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ShellConfigurationTest extends TestCase
{
    /** @test */
    function it_gets_the_value_of_of_a_shell_environment_variable()
    {
        Arr::set($_SERVER, 'SHELL_ENVIRONMENT_VARIABLE', 'foo');

        $shellConfiguration = new ShellConfiguration([
            'SHELL_ENVIRONMENT_VARIABLE' => 'genericOption',
        ]);

        $this->assertEquals('foo', $shellConfiguration->genericOption);
    }

    /** @test */
    function it_returns_null_if_a_shell_environment_variable_is_missing()
    {
        Arr::forget($_SERVER, 'SHELL_ENVIRONMENT_VARIABLE');

        $shellConfiguration = new ShellConfiguration([
            'SHELL_ENVIRONMENT_VARIABLE' => 'genericOption',
        ]);

        $this->assertNull($shellConfiguration->genericOption);
    }

    /** @test */
    function it_returns_null_if_a_shell_environment_variable_is_empty()
    {
        Arr::set($_SERVER, 'SHELL_ENVIRONMENT_VARIABLE', '');

        $shellConfiguration = new ShellConfiguration([
            'SHELL_ENVIRONMENT_VARIABLE' => 'genericOption',
        ]);

        $this->assertNull($shellConfiguration->genericOption);
    }

    /** @test */
    function it_returns_null_if_a_non_existent_property_is_requested()
    {
        $shellConfiguration = new ShellConfiguration([]);
        $this->assertNull($shellConfiguration->foo);
    }
}
