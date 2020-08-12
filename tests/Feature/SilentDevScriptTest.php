<?php

namespace Tests\Feature;

use App\Actions\SilentDevScript;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SilentDevScriptTest extends TestCase
{
    /** @test */
    function it_adds_a_silent_asset_compilation_script_and_makes_a_backup_of_the_original()
    {
        Config::set('lambo.store.project_path', '/some/project/path');

        $packageJson = File::get(base_path('tests/Feature/Fixtures/package.json'));
        $silentPackageJson = File::get(base_path('tests/Feature/Fixtures/package-silent.json'));

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
