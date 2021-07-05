<?php

namespace Tests\Feature;

use App\Actions\ValidateGithubConfiguration;
use App\Configuration\LamboConfiguration;
use App\ConsoleWriter;
use LaravelZero\Framework\Commands\Command;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

/**
 * @group git-and-github
 */
class ValidateGithubConfigurationTest extends TestCase
{
    private $consoleWriter;
    private $console;

    /** @test */
    function it_skips_gh_command_line_tool_validation()
    {
        config(['lambo.store.tools.gh' => true]);
        config(['lambo.store.tools.hub' => true]);

        config(['lambo.store.initializeGitHub' => null]);
        app(ValidateGithubConfiguration::class)();
        $this->assertFalse(config('lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB));

        config(['lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB => false]);
        app(ValidateGithubConfiguration::class)();
        $this->assertFalse(config('lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB));
    }

    /** @test */
    function it_logs_a_warning_if_github_tooling_is_missing()
    {
        $this->consoleWriter = $this->mock(ConsoleWriter::class);
        $this->console = $this->mock(Command::class);

        config(['lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB => true]);
        config(['lambo.store.tools.gh' => false]);
        config(['lambo.store.tools.hub' => false]);

        $this->shouldLogWarning();
        $this->shouldLogInstructions(ValidateGithubConfiguration::INSTRUCTIONS_GITHUB_TOOLING_MISSING);
        $this->shouldAskToContinue();

        app(ValidateGithubConfiguration::class)();

        $this->assertFalse(config('lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB));
    }

    /** @test */
    function configuration_is_valid_if_hub_is_installed()
    {
        $this->consoleWriter = $this->mock(ConsoleWriter::class);

        config(['lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB => true]);
        config(['lambo.store.tools.gh' => true]);
        config(['lambo.store.tools.hub' => true]);

        app(ValidateGithubConfiguration::class)();

        $this->assertTrue(config('lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB));
    }

    /** @test */
    function configuration_is_valid_if_gh_is_installed_and_authenticated()
    {
        $this->consoleWriter = $this->mock(ConsoleWriter::class);

        config(['lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB => true]);
        config(['lambo.store.tools.gh' => true]);
        config(['lambo.store.tools.hub' => false]);

        $this->shell->shouldReceive('execQuietly')
            ->with('gh auth status')
            ->andReturn(FakeProcess::success());

        app(ValidateGithubConfiguration::class)();

        $this->assertTrue(config('lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB));
    }

    /** @test */
    function it_logs_a_warning_if_gh_is_not_authenticated_with_github()
    {
        $this->consoleWriter = $this->mock(ConsoleWriter::class);
        $this->console = $this->mock(Command::class);

        config(['lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB => true]);
        config(['lambo.store.tools.gh' => true]);
        config(['lambo.store.tools.hub' => false]);

        $this->shell->shouldReceive('execQuietly')
            ->with('gh auth status')
            ->andReturn(FakeProcess::fail('gh auth status'));

        $this->shouldLogWarning();
        $this->shouldLogInstructions(ValidateGithubConfiguration::INSTRUCTIONS_GH_NOT_AUTHENTICATED);
        $this->shouldAskToContinue();

        app(ValidateGithubConfiguration::class)();

        $this->assertFalse(config('lambo.store.' . LamboConfiguration::INITIALIZE_GITHUB));
    }

    private function shouldLogWarning(): void
    {
        $this->consoleWriter->shouldReceive('warn')
            ->with(ValidateGithubConfiguration::WARNING_UNABLE_TO_CREATE_REPOSITORY)
            ->globally()
            ->ordered();
    }

    private function shouldAskToContinue(): void
    {
        $this->console->shouldReceive('confirm')
            ->with(ValidateGithubConfiguration::QUESTION_SHOULD_CONTINUE)
            ->andReturnTrue()
            ->globally()
            ->ordered();
        $this->swap('console', $this->console);
    }

    private function shouldLogInstructions(array $instructions): void
    {
        $this->consoleWriter->shouldReceive('text')
            ->with($instructions)
            ->globally()
            ->ordered();
    }
}
