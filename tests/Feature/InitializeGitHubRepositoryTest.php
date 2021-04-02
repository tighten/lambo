<?php

namespace Tests\Feature;

use App\Actions\InitializeGitHubRepository;
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

    /** @test */
    function it_skips_repository_creation()
    {
        $this->withConfig([
            'github' => false,
        ]);

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_initialises_a_new_git_hub_repository()
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
