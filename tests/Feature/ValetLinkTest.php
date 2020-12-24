<?php

namespace Tests\Feature;

use App\Actions\ValetLink;
use App\LamboException;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class ValetLinkTest extends TestCase
{
    /** @test */
    function it_runs_valet_link()
    {
        config(['lambo.store.valet_link' => true]);

        $this->shell->shouldReceive('execInProject')
            ->with('valet link')
            ->once()
            ->andReturn(FakeProcess::success());

        app(ValetLink::class)();
    }

    /** @test */
    function it_throws_an_exception_if_valet_link_fails()
    {
        config(['lambo.store.valet_link' => true]);

        $command = 'valet link';
        $this->shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command));

        $this->expectException(LamboException::class);

        app(ValetLink::class)();
    }
}
