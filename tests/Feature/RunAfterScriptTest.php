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
    /** @test */
    function it_runs_the_after_script_if_one_exists()
    {
        $shell = $this->mock(Shell::class);

        Config::set('home_dir', '/my/home/dir');
        $afterScriptPath = Config::get('home_dir') . '/.lambo/after';

        File::shouldReceive('exists')
            ->with($afterScriptPath)
            ->andReturn(true)
            ->globally()
            ->ordered();

        $shell->shouldReceive('execInProject')
            ->with("sh " . $afterScriptPath)
            ->once()
            ->andReturn(FakeProcess::success())
            ->globally()
            ->ordered();

        app(RunAfterScript::class)();
    }

    /** @test */
    function it_throws_an_exception_if_the_after_script_fails()
    {
        $shell = $this->mock(Shell::class);

        Config::set('home_dir', '/my/home/dir');
        $afterScriptPath = Config::get('home_dir') . '/.lambo/after';

        File::shouldReceive('exists')
            ->with($afterScriptPath)
            ->andReturn(true)
            ->globally()
            ->ordered();

        $command = "sh " . $afterScriptPath;
        $shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command))
            ->globally()
            ->ordered();

        $this->expectException(Exception::class);

        app(RunAfterScript::class)();
    }
}
