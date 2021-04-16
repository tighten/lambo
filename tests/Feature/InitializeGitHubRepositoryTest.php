<?php

namespace Tests\Feature;

use App\Actions\InitializeGitHubRepository;
use App\ConsoleWriter;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

/**
 * @group git-and-github
 */
class InitializeGitHubRepositoryTest extends TestCase
{
    private $ghRepoCreateCommand = 'gh repo create --confirm';
    private $newProjectName = 'project';
    private $ghRepoCreateOptions = '--options --to --pass --to --gh --repo --create';
    private $defaultBranchName = 'branch';

    function setUp(): void
    {
        parent::setUp();
        $this->consoleWriter = $this->mock(ConsoleWriter::class);
        $this->consoleWriter->shouldReceive('logStep');
        $this->consoleWriter->shouldReceive('success');
    }

    /** @test */
    function it_skips_repository_creation()
    {
        $this->withConfig([
            'github' => null,
        ]);

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_initialises_a_new_github_repository()
    {
        $this->withConfig();

        $this->shell->shouldReceive('execInProject')
            ->with("{$this->ghRepoCreateCommand} {$this->newProjectName} {$this->ghRepoCreateOptions}")
            ->once()
            ->andReturn(FakeProcess::success());

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_initialises_a_repository_for_the_given_organisation()
    {
        $this->withConfig([
            'github-org' => 'org',
        ]);

        $this->shell->shouldReceive('execInProject')
            ->with("{$this->ghRepoCreateCommand} org/{$this->newProjectName} {$this->ghRepoCreateOptions}")
            ->once()
            ->andReturn(FakeProcess::success());

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_warns_the_user_if_repository_creation_fails()
    {
        $this->withConfig();

        $command = "{$this->ghRepoCreateCommand} {$this->newProjectName} {$this->ghRepoCreateOptions}";
        $failedCommandOutput = 'Failed command output';

        $this->shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command)->withErrorOutput($failedCommandOutput));

        $this->consoleWriter->shouldReceive('warn')
            ->with(InitializeGitHubRepository::WARNING_FAILED_TO_CREATE_REPOSITORY)
            ->globally()
            ->ordered();

        $this->consoleWriter->shouldReceive('warnCommandFailed')
            ->with($command)
            ->globally()
            ->ordered();

        $this->consoleWriter->shouldReceive('showOutputErrors')
            ->with($failedCommandOutput)
            ->globally()
            ->ordered();

        app(InitializeGitHubRepository::class)();
    }

    function withConfig(array $overrides = []): void
    {
        config([
            'lambo.store' => array_merge([
                'github' => $this->ghRepoCreateOptions,
                'project_name' => $this->newProjectName,
                'branch' => $this->defaultBranchName,
            ], $overrides)
        ]);
    }
}
