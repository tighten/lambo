<?php

namespace Tests\Feature;

use App\Actions\CompileAssets;
use App\Actions\SilentDevScript;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class CompileAssetsTest extends TestCase
{
    private $shell;

    private $silentDevScript;

    public function setUp(): void
    {
        parent::setUp();
        $this->silentDevScript = $this->mock(SilentDevScript::class);
        $this->shell = $this->mock(Shell::class);
    }

    /** @test */
    function it_compiles_project_assets_and_hides_console_output()
    {
        Config::set('lambo.store.mix', true);

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
    function it_compiles_project_assets_and_shows_console_output()
    {
        Config::set('lambo.store.mix', true);
        Config::set('lambo.store.with_output', true);

        $this->silentDevScript->shouldReceive('add')
            ->once()
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('execInProject')
            ->with('npm run dev')
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
    function it_skips_asset_compilation_if_it_is_not_requested()
    {
        $this->silentDevScript = $this->spy(SilentDevScript::class);
        $this->shell = $this->spy(Shell::class);

        Config::set('lambo.store.mix', false);

        app(CompileAssets::class)();

        $this->silentDevScript->shouldNotHaveReceived('add');
        $this->shell->shouldNotHaveReceived('execInProject');
        $this->silentDevScript->shouldNotHaveReceived('remove');
    }

    /** @test */
    function it_throws_an_exception_if_asset_compilation_fails()
    {
        Config::set('lambo.store.mix', true);

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

        $this->expectException(Exception::class);

        app(CompileAssets::class)();
    }
}
