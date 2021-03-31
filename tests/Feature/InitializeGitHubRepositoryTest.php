<?php

namespace Tests\Feature;

use App\Actions\InitializeGitHubRepository;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class InitializeGitHubRepositoryTest extends TestCase
{
    private $ghRepoCreateCommand = 'gh repo create --confirm';
    private $newProjectName = 'project';
    private $ghRepoCreateOptions = '--options --to --pass --to --gh --repo --create';
    private $defaultBranchName = 'branch';

    /** @test */
    function it_initialises_the_repository()
    {
        $this->withConfig();

        $this->shell->shouldReceive('execInProject')
            ->with("{$this->ghRepoCreateCommand} {$this->newProjectName} {$this->ghRepoCreateOptions}")
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shouldPushToGitHub();

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

        $this->shouldPushToGitHub();

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_registers_a_lambo_summary_warning_if_execution_fails()
    {
        $this->markTestIncomplete('[ Incomplete ] It registers a lambo summary warning if execution fails');
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

    protected function shouldPushToGitHub(): void
    {
        $this->shell->shouldReceive('execInProject')
            ->with("git -c credential.helper= -c credential.helper='!gh auth git-credential' push -u origin branch")
            ->once()
            ->andReturn(FakeProcess::success());
    }
}
