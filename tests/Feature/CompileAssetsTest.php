<?php

namespace Tests\Feature;

use App\Actions\CompileAssets;
use App\Actions\SilentDevScript;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class CompileAssetsTest extends TestCase
{
    /** @test */
    function it_compiles_project_assets_and_hides_console_output()
    {
        $this->fakeLamboConsole();

        $silentDevScript = $this->mock(SilentDevScript::class);
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.mix', true);

        $silentDevScript->shouldReceive('add')
            ->once()
            ->globally()
            ->ordered();

        $shell->shouldReceive('execInProject')
            ->with('npm run dev --silent')
            ->once()
            ->andReturn(FakeProcess::success())
            ->globally()
            ->ordered();

        $silentDevScript->shouldReceive('remove')
            ->once()
            ->globally()
            ->ordered();

        app(CompileAssets::class)();
    }

    /** @test */
    function it_compiles_project_assets_and_shows_console_output()
    {
        $this->fakeLamboConsole();
        $silentDevScript = $this->mock(SilentDevScript::class);
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.mix', true);
        Config::set('lambo.store.with_output', true);

        $silentDevScript->shouldReceive('add')
            ->once()
            ->globally()
            ->ordered();

        $shell->shouldReceive('execInProject')
            ->with('npm run dev')
            ->once()
            ->andReturn(FakeProcess::success())
            ->globally()
            ->ordered();

        $silentDevScript->shouldReceive('remove')
            ->once()
            ->globally()
            ->ordered();

        app(CompileAssets::class)();
    }

    /** @test */
    function it_throws_an_exception_if_asset_compilation_fails()
    {
        $this->fakeLamboConsole();
        $silentDevScript = $this->mock(SilentDevScript::class);
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.mix', true);

        $silentDevScript->shouldReceive('add')
            ->once()
            ->globally()
            ->ordered();

        $command = 'npm run dev --silent';
        $shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command))
            ->globally()
            ->ordered();

        $this->expectException(Exception::class);

        app(CompileAssets::class)();
    }

    /** @test */
    function it_adds_a_silent_asset_compilation_script_and_makes_a_backup_of_the_original()
    {
        Config::set('lambo.store.project_path', '/some/project/path');

        $packageJson = File::get(base_path('tests/feature/fixtures/package.json'));
        $silentPackageJson = File::get(base_path('tests/feature/fixtures/package-silent.json'));

        File::shouldReceive('copy')
            ->with('/some/project/path/package.json', '/some/project/path/package-original.json')
            ->once()
            ->globally()
            ->ordered();

        File::shouldReceive('get')
            ->with('/some/project/path/package.json')
            ->once()
            ->andReturn($packageJson)
            ->globally()
            ->ordered();

        File::shouldReceive('replace')
            ->with('/some/project/path/package.json', trim($silentPackageJson))
            ->once()
            ->globally()
            ->ordered();

        app(SilentDevScript::class)->add();
    }

    /** @test */
    function it_replaces_the_silent_compilation_script_with_the_original()
    {
        Config::set('lambo.store.project_path', '/some/project/path');

        File::shouldReceive('move')
            ->with('/some/project/path/package-original.json', '/some/project/path/package.json')
            ->once();

        app(SilentDevScript::class)->remove();
    }
}
