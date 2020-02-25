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
    private $shell;

    public function setUp(): void
    {
        parent::setUp();
        $this->shell = $this->mock(Shell::class);
    }

    /** @test */
    function it_initialises_the_projects_git_repository()
    {
        Config::set('lambo.store.commit_message', 'Initial commit');

        $this->shell->shouldReceive('execInProject')
            ->with('git init')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shell->shouldReceive('execInProject')
            ->with('git add .')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shell->shouldReceive('execInProject')
            ->with('git commit -m "' . 'Initial commit' . '"')
            ->once()
            ->andReturn(FakeProcess::success());

        app(InitializeGitRepo::class)();
    }

    /** @test */
    function it_throws_an_exception_if_git_init_fails()
    {
        $this->shell->shouldReceive('execInProject')
            ->with('git init')
            ->once()
            ->andReturn(FakeProcess::fail('git init'));

        $this->expectException(Exception::class);

        app(InitializeGitRepo::class)();
    }

    /** @test */
    function it_throws_an_exception_if_git_add_fails()
    {
        $this->shell->shouldReceive('execInProject')
            ->with('git init')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shell->shouldReceive('execInProject')
            ->with('git add .')
            ->once()
            ->andReturn(FakeProcess::fail('git add .'));

        $this->expectException(Exception::class);

        app(InitializeGitRepo::class)();
    }

    /** @test */
    function it_throws_an_exception_if_git_commit_fails()
    {
        Config::set('lambo.store.commit_message', 'Initial commit');

        $command = 'git init';
        $this->shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shell->shouldReceive('execInProject')
            ->with('git add .')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->shell->shouldReceive('execInProject')
            ->with('git commit -m "Initial commit"')
            ->once()
            ->andReturn(FakeProcess::fail('git commit -m "Initial commit"'));

        $this->expectException(Exception::class);

        app(InitializeGitRepo::class)();
    }
}
