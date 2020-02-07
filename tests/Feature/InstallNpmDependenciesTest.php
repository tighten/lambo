<?php

namespace Tests\Feature;

use App\Actions\InstallNpmDependencies;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class InstallNpmDependenciesTest extends TestCase
{
    /** @test */
    function it_installs_npm_dependencies()
    {
        $this->fakeLamboConsole();

        Config::set('lambo.store.node', true);
        Config::set('lambo.store.with-output', false);

        $this->instance(Shell::class, Mockery::mock(Shell::class, function ($shell) {
            $shell->shouldReceive('execInProject')
                ->with("npm install --silent")
                ->once()
                ->andReturn(FakeProcess::success());
        }));

        app(InstallNpmDependencies::class)();
    }

    /** @test */
    function it_installs_npm_dependencies_and_shows_console_output()
    {
        $this->fakeLamboConsole();

        Config::set('lambo.store.node', true);
        Config::set('lambo.store.with-output', true);

        $this->instance(Shell::class, Mockery::mock(Shell::class, function ($shell) {
            $shell->shouldReceive('execInProject')
                ->with("npm install")
                ->once()
                ->andReturn(FakeProcess::success());
        }));

        app(InstallNpmDependencies::class)();
    }

    /** @test */
    function it_throws_an_exception_if_npm_install_fails()
    {
        $this->fakeLamboConsole();

        Config::set('lambo.store.node', true);
        Config::set('lambo.store.with-output', false);

        $this->mock(Shell::class, function ($shell) {
            $command = 'npm install --silent';
            $shell->shouldReceive('execInProject')
                ->with($command)
                ->once()
                ->andReturn(FakeProcess::fail($command));
        });

        $this->expectException(Exception::class);

        app(InstallNpmDependencies::class)();
    }
}
