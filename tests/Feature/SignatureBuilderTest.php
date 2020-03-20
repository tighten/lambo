<?php

namespace Tests\Feature;

use App\Commands\NewCommand;
use Tests\TestCase;

class SignatureBuilderTest extends TestCase
{
    /** @test */
    function it_offers_short_and_long_codes()
    {
        $newCommand = new NewCommand;
        $output = $newCommand->buildSignatureOption([
            'short' => 'e',
            'long' => 'editor',
            'cli_description' => '',
        ]);

        $this->assertStringContainsString('-e|editor', $output);
    }

    /** @test */
    function it_functions_given_only_long_code()
    {
        $newCommand = new NewCommand;
        $output = $newCommand->buildSignatureOption([
            'long' => 'editor',
            'cli_description' => '',
        ]);

        $this->assertStringContainsString('-editor', $output);
    }

    /** @test */
    function it_sets_expectation_for_values_if_option_expects_parameters()
    {
        $newCommand = new NewCommand;
        $output = $newCommand->buildSignatureOption([
            'long' => 'editor',
            'param_description' => 'a',
            'cli_description' => '',
        ]);

        $this->assertStringContainsString('-editor=', $output);
    }

    /** @test */
    function it_does_not_set_expectation_for_values_if_option_does_not_expect_parameters()
    {
        $newCommand = new NewCommand;
        $output = $newCommand->buildSignatureOption([
            'long' => 'editor',
            'cli_description' => '',
        ]);

        $this->assertStringNotContainsString('editor=', $output);
    }

    /** @test */
    function it_defines_description()
    {
        $newCommand = new NewCommand;
        $output = $newCommand->buildSignatureOption([
            'long' => 'editor',
            'cli_description' => 'The Option Description',
        ]);

        $this->assertStringContainsString('The Option Description', $output);
    }
}
