<?php

namespace Tests\Feature;

use App\Actions\ValetSecure;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class ValetSecureTest extends TestCase
{
    /** @test */
    function it_runs_valet_link()
    {
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.valet_secure', true);

        $shell->shouldReceive('execInProject')
            ->with('valet secure')
            ->once()
            ->andReturn(FakeProcess::success());

        app(ValetSecure::class)();
    }

    /** @test */
    function it_throws_an_exception_if_the_after_script_fails()
    {
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.valet_secure', true);

        $command = 'valet secure';
        $shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command));

        $this->expectException(Exception::class);

        app(ValetSecure::class)();
    }
}
