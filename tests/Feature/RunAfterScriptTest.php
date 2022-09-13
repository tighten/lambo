<?php

namespace Tests\Feature;

use App\Actions\RunAfterScript;
use App\LamboException;
use Illuminate\Support\Facades\File;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class RunAfterScriptTest extends TestCase
{
    function setUp(): void
    {
        parent::setUp();
        config(['home_dir' => $this->getHomeDirectory()]);
        config(['lambo.store.project_path' => $this->getProjectPath()]);
    }

    /** @test */
    function it_runs_the_after_script_if_one_exists()
    {
        File::shouldReceive('isFile')
            ->with($this->getAfterScriptPath())
            ->andReturn(true)
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('withTTY')
            ->once()
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('execInProject')
            ->with($this->getCommand())
            ->once()
            ->andReturn(FakeProcess::success())
            ->globally()
            ->ordered();

        app(RunAfterScript::class)();
    }

    /** @test */
    function it_throws_an_exception_if_the_after_script_fails()
    {
        File::shouldReceive('isFile')
            ->with($this->getAfterScriptPath())
            ->andReturn(true)
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('withTTY')
            ->once()
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('execInProject')
            ->with($this->getCommand())
            ->once()
            ->andReturn(FakeProcess::fail($this->getCommand()))
            ->globally()
            ->ordered();

        $this->expectException(LamboException::class);

        app(RunAfterScript::class)();
    }

    private function getAfterScriptPath(): string
    {
        return "{$this->getHomeDirectory()}/.lambo/after";
    }

    private function getHomeDirectory(): string
    {
        return '/my/home/dir';
    }

    private function getCommand(): string
    {
        return sprintf('env PROJECTPATH=%s sh %s', $this->getProjectPath(), $this->getAfterScriptPath());
    }

    private function getProjectPath(): string
    {
        return '/my/project/path';
    }
}
