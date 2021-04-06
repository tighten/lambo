<?php

namespace Tests\Feature;

use App\Actions\ValidateGithubConfiguration;
use App\ConsoleWriter;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\ExecutableFinder;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

/**
 * @group git-and-github
 */
class ValidateGithubConfigurationTest extends TestCase
{
    protected $consoleWriter;

    private $executableFinder;
    private $console;

    function setUp(): void
    {
        parent::setUp();
        $this->executableFinder = $this->mock(ExecutableFinder::class);
        $this->consoleWriter = $this->mock(ConsoleWriter::class);
        $this->console = $this->mock(Command::class);

        config(['lambo.store.github' => 'foo']);
    }

    /** @test */
    function it_validates_the_gh_command_line_tool_configuration()
    {
        $this->executableFinder->shouldReceive('find')
            ->with('gh')
            ->andReturn('/path/to/github');

        $this->shell->shouldReceive('execQuietly')
            ->with('gh auth status')
            ->andReturn(FakeProcess::success());

        app(ValidateGithubConfiguration::class)();
    }

    /** @test */
    function it_skips_gh_command_line_tool_validation()
    {
        config(['lambo.store.github' => null]);
        app(ValidateGithubConfiguration::class)();
    }

    /** @test */
    function it_logs_a_warning_if_the_gh_command_line_tool_is_missing()
    {
        $this->executableFinder->shouldReceive('find')
            ->with('gh')
            ->andReturnNull();

        $this->shouldLogWarning();
        $this->shouldLogInstructions(ValidateGithubConfiguration::INSTRUCTIONS_GH_NOT_INSTALLED);
        $this->shouldAskToContinue();

        app(ValidateGithubConfiguration::class)();
    }

    /** @test */
    function it_logs_a_warning_if_the_gh_command_line_tool_is_not_authenticated_with_github()
    {
        $this->executableFinder->shouldReceive('find')
            ->with('gh')
            ->andReturn('/path/to/github');

        $this->shell->shouldReceive('execQuietly')
            ->with('gh auth status')
            ->andReturn(FakeProcess::fail('gh auth status'));

        $this->shouldLogWarning();
        $this->shouldLogInstructions(ValidateGithubConfiguration::INSTRUCTIONS_GH_NOT_AUTHENTICATED);
        $this->shouldAskToContinue();

        app(ValidateGithubConfiguration::class)();
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
