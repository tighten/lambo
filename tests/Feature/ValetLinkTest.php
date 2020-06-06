<?php

namespace Tests\Feature;

use App\Actions\ValetLink;
use App\LamboException;
use App\Shell;
use Illuminate\Support\Facades\Config;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class ValetLinkTest extends TestCase
{
    private $shell;

    public function setUp(): void
    {
        parent::setUp();
        $this->shell = $this->mock(Shell::class);
    }

    /** @test */
    function it_runs_valet_link()
    {
        Config::set('lambo.store.valet_link', true);

        $this->shell->shouldReceive('execInProject')
            ->with('valet link')
            ->once()
            ->andReturn(FakeProcess::success());

        app(ValetLink::class)();
    }

    /** @test */
    function it_throws_an_exception_if_valet_link_fails()
    {
        Config::set('lambo.store.valet_link', true);

        $command = 'valet link';
        $this->shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command));

        $this->expectException(LamboException::class);

        app(ValetLink::class)();
    }
}
