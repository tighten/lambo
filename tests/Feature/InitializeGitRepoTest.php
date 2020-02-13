<?php

namespace Tests\Feature;

use App\Actions\InitializeGitRepo;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class InitializeGitRepoTest extends TestCase
{
    /** @test */
    function it_initialises_the_projects_git_repository()
    {
        $commitMessage = 'Initial commit';
        Config::set('lambo.store.commit_message', $commitMessage);

        $this->mock(Shell::class, function($shell) use ($commitMessage) {
            $shell->shouldReceive('execInProject')
                ->with('git init')
                ->once()
                ->andReturn(FakeProcess::success());

            $shell->shouldReceive('execInProject')
                ->with('git add .')
                ->once()
                ->andReturn(FakeProcess::success());

            $shell->shouldReceive('execInProject')
                ->with('git commit -m "' . $commitMessage . '"')
                ->once()
                ->andReturn(FakeProcess::success());
        });

        app(InitializeGitRepo::class)();
    }

    /** @test */
    function it_throws_an_exception_if_git_init_fails()
    {
        $command = 'git init';
        $this->mock(Shell::class, function($shell) use ($command) {
            $shell->shouldReceive('execInProject')
                ->with($command)
                ->once()
                ->andReturn(FakeProcess::fail($command));
        });

        $this->expectException(Exception::class);

        app(InitializeGitRepo::class)();
    }

    /** @test */
    function it_throws_an_exception_if_git_add_fails()
    {
        $command = 'git add .';
        $this->mock(Shell::class, function($shell) use ($command) {
            $shell->shouldReceive('execInProject')
                ->with('git init')
                ->once()
                ->andReturn(FakeProcess::success());

            $shell->shouldReceive('execInProject')
                ->with($command)
                ->once()
                ->andReturn(FakeProcess::fail($command));
        });

        $this->expectException(Exception::class);

        app(InitializeGitRepo::class)();
    }

    /** @test */
    function it_throws_an_exception_if_git_commit_fails()
    {
        $commitMessage = 'Initial commit';
        Config::set('lambo.store.commit_message', $commitMessage);

        $command = 'git commit -m "' . $commitMessage . '"';
        $this->mock(Shell::class, function($shell) use ($command) {
            $shell->shouldReceive('execInProject')
                ->with('git init')
                ->once()
                ->andReturn(FakeProcess::success());

            $shell->shouldReceive('execInProject')
                ->with('git add .')
                ->once()
                ->andReturn(FakeProcess::success());

            $shell->shouldReceive('execInProject')
                ->with($command)
                ->once()
                ->andReturn(FakeProcess::fail($command));
        });

        $this->expectException(Exception::class);

        app(InitializeGitRepo::class)();
    }
}
