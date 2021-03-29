<?php

namespace Tests\Feature;

use App\Actions\InitializeGitHubRepository;
use App\LamboException;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class InitializeGitHubRepositoryTest extends TestCase
{
    /** @test */
    function it_initialises_the_repository()
    {
        $this->withConfig();

        $this->shell->shouldReceive('execInProject')
            ->with('gh repo create my-project --confirm --private')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shouldPushToGitHub();

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_initialises_a_repository_for_the_given_organisation()
    {
        $this->withConfig([
            'github' => [
                'organization' => 'acme',
            ],
        ]);

        $this->shell->shouldReceive('execInProject')
            ->with('gh repo create acme/my-project --confirm --private')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shouldPushToGitHub();

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_initialises_a_repository_with_the_specified_visibility()
    {
        $this->withConfig([
            'github' => [
                'visibility' => '--foo-visibility',
            ],
        ]);

        $this->shell->shouldReceive('execInProject')
            ->with('gh repo create my-project --confirm --foo-visibility')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shouldPushToGitHub();

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_initialises_a_repository_without_issue_management()
    {
        $this->withConfig([
            'github' => [
                'no-issues' => true,
            ],
        ]);

        $this->shell->shouldReceive('execInProject')
            ->with('gh repo create my-project --confirm --private --enable-issues=false')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shouldPushToGitHub();

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_initialises_a_repository_without_a_wiki()
    {
        $this->withConfig([
            'github' => [
                'no-wiki' => true,
            ],
        ]);

        $this->shell->shouldReceive('execInProject')
            ->with('gh repo create my-project --confirm --private --enable-wiki=false')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shouldPushToGitHub();

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_initialises_a_repository_with_the_given_description()
    {
        $this->withConfig([
            'github' => [
                'description' => 'YATLA (Yet another todo list app)',
            ],
        ]);

        $this->shell->shouldReceive('execInProject')
            ->with("gh repo create my-project --confirm --private --description='YATLA (Yet another todo list app)'")
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shouldPushToGitHub();

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_initialises_a_repository_with_the_given_homepage_url()
    {
        $this->withConfig([
            'github' => [
                'homepage' => 'https://example.com',
            ],
        ]);

        $this->shell->shouldReceive('execInProject')
            ->with("gh repo create my-project --confirm --private --homepage='https://example.com'")
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shouldPushToGitHub();

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_initialises_a_repository_with_the_given_team_access()
    {
        $this->withConfig([
            'github' => [
                'team' => 'foo-team',
            ],
        ]);

        $this->shell->shouldReceive('execInProject')
            ->with("gh repo create my-project --confirm --private --team='foo-team'")
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shouldPushToGitHub();

        app(InitializeGitHubRepository::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_github_repository_creation_fails()
    {
        $this->skipWithMessage([
            'We should not abort if GitHub repository creation fails.',
            'Perhaps we warn the user?',
        ]);

        config(['lambo.store.initialize_github' => true]);
        config(['lambo.store.project_name' => 'my-project']);

        $command = 'gh repo create my-project --confirm --private';
        $this->shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command));

        $this->expectException(LamboException::class);

        app(InitializeGitHubRepository::class)();
    }

    function withConfig(array $overrides = []): void
    {
        config([
            'lambo.store' => array_merge([
                'initialize_github' => true,
                'project_name' => 'my-project',
                'branch' => 'foo-branch',
            ], $overrides)
        ]);
    }

    protected function shouldPushToGitHub(): void
    {
        $this->shell->shouldReceive('execInProject')
            ->with("git -c credential.helper= -c credential.helper='!gh auth git-credential' push -u origin foo-branch")
            ->once()
            ->andReturn(FakeProcess::success());
    }
}
