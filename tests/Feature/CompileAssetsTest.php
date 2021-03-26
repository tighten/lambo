<?php

namespace Tests\Feature;

use App\Actions\CompileAssets;
use App\Actions\SilenceNpm;
use App\LamboException;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class CompileAssetsTest extends TestCase
{
    private $npm;

    public function setUp(): void
    {
        parent::setUp();
        $this->npm = $this->mock(SilenceNpm::class);
    }

    /** @test */
    function it_compiles_project_assets_and_hides_console_output()
    {
        $this->npm->shouldReceive('silence')
            ->once()
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('execInProject')
            ->with('npm run dev --silent')
            ->once()
            ->andReturn(FakeProcess::success())
            ->globally()
            ->ordered();

        $this->npm->shouldReceive('unsilence')
            ->once()
            ->globally()
            ->ordered();

        app(CompileAssets::class)();
    }

    /** @test */
    function it_throws_an_exception_if_asset_compilation_fails()
    {
        $this->npm->shouldReceive('silence')
            ->once()
            ->globally()
            ->ordered();

        $command = 'npm run dev --silent';
        $this->shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command))
            ->globally()
            ->ordered();

        $this->expectException(LamboException::class);

        app(CompileAssets::class)();
    }
}
