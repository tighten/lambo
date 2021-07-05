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
    /** @test */
    function it_pushes_to_github()
    {
        config(['lambo.store.push_to_github' => true]);
        config(['lambo.store.branch' => 'branch']);

        $this->shell->shouldReceive('execInProject')
            ->with('git push -u origin branch')
            ->once()
            ->andReturn(FakeProcess::success());

        app(PushToGitHub::class)();
    }

    /** @test */
    function it_skips_pushing_to_github()
    {
        config(['lambo.store.branch' => 'branch']);
        app(PushToGitHub::class)();

        config(['lambo.store.push_to_github' => false]);
        config(['lambo.store.branch' => 'branch']);
        app(PushToGitHub::class)();
    }
}
