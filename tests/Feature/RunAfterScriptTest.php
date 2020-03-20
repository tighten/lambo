<?php

namespace Tests\Feature;

use App\Actions\RunAfterScript;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class RunAfterScriptTest extends TestCase
{
    private $shell;

    public function setUp(): void
    {
        parent::setUp();
        $this->shell = $this->mock(Shell::class);
    }

    /** @test */
    function it_runs_the_after_script_if_one_exists()
    {
        Config::set('home_dir', '/my/home/dir');

        File::shouldReceive('exists')
            ->with('/my/home/dir/.lambo/after')
            ->andReturn(true)
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('execInProject')
            ->with('sh /my/home/dir/.lambo/after')
            ->once()
            ->andReturn(FakeProcess::success())
            ->globally()
            ->ordered();

        app(RunAfterScript::class)();
    }

    /** @test */
    function it_throws_an_exception_if_the_after_script_fails()
    {
        Config::set('home_dir', '/my/home/dir');

        File::shouldReceive('exists')
            ->with('/my/home/dir/.lambo/after')
            ->andReturn(true)
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('execInProject')
            ->with('sh /my/home/dir/.lambo/after')
            ->once()
            ->andReturn(FakeProcess::fail('sh /my/home/dir/.lambo/after'))
            ->globally()
            ->ordered();

        $this->expectException(Exception::class);

        app(RunAfterScript::class)();
    }
}
