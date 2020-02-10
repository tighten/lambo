<?php

namespace Tests\Feature;

use App\Actions\ValetLink;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class ValetLinkTest extends TestCase
{
    /** @test */
    function it_runs_valet_link()
    {
        $this->fakeLamboConsole();
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.valet_link', true);

        $shell->shouldReceive('execInProject')
            ->with('valet link')
            ->once()
            ->andReturn(FakeProcess::success());

        app(ValetLink::class)();
    }

    /** @test */
    function it_throws_an_exception_if_the_after_script_fails()
    {
        $this->fakeLamboConsole();
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.valet_link', true);

        $command = 'valet link';
        $shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command));

        $this->expectException(Exception::class);

        app(ValetLink::class)();
    }
}
