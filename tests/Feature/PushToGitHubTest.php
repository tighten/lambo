<?php

namespace Tests\Feature;

use App\Actions\PushToGitHub;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

/**
 * @group git-and-github
 */
class PushToGitHubTest extends TestCase
{
    private $gitPushCommand = "git -c credential.helper= -c credential.helper='!gh auth git-credential' push -u origin";
    private $defaultBranchName = 'branch';

    /** @test */
    function it_pushes_to_github()
    {
        $this->withConfig();

        $this->shell->shouldReceive('execInProject')
            ->with("git -c credential.helper= -c credential.helper='!gh auth git-credential' push -u origin branch")
            ->once()
            ->andReturn(FakeProcess::success());

        app(PushToGitHub::class)();
    }

    /** @test */
    function it_skips_pushing_to_github()
    {
        $this->markTestSkipped('-- Pending -- "it skips pushing to github"');
    }

    function withConfig(array $overrides = []): void
    {
        config([
            'lambo.store' => array_merge([
                'branch' => $this->defaultBranchName,
                'push_to_github' => true,
            ], $overrides)
        ]);
    }
}
