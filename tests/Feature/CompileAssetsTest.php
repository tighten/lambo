<?php

namespace Tests\Feature;

use App\Actions\CompileAssets;
use App\Actions\SilentDevScript;
use App\LamboException;
use App\Shell;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class CompileAssetsTest extends TestCase
{
    private $silentDevScript;

    public function setUp(): void
    {
        parent::setUp();
        $this->silentDevScript = $this->mock(SilentDevScript::class);
        }

    /** @test */
    function it_compiles_project_assets_and_hides_console_output()
    {
        $this->silentDevScript->shouldReceive('add')
            ->once()
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('execInProject')
            ->with('npm run dev --silent')
            ->once()
            ->andReturn(FakeProcess::success())
            ->globally()
            ->ordered();

        $this->silentDevScript->shouldReceive('remove')
            ->once()
            ->globally()
            ->ordered();

        app(CompileAssets::class)();
    }

    /** @test */
    function it_throws_an_exception_if_asset_compilation_fails()
    {
        $this->silentDevScript->shouldReceive('add')
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
