<?php

namespace Tests\Feature;

use App\Actions\InstallNpmDependencies;
use App\LamboException;
use App\Shell;
use Illuminate\Support\Facades\Config;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class InstallNpmDependenciesTest extends TestCase
{
    private $shell;

    public function setUp(): void
    {
        parent::setUp();
        $this->shell = $this->mock(Shell::class);
    }

    /** @test */
    function it_installs_npm_dependencies()
    {
        Config::set('lambo.store.node', true);
        Config::set('lambo.store.with_output', false);

        $this->shell->shouldReceive('execInProject')
            ->with("npm install --silent")
            ->once()
            ->andReturn(FakeProcess::success());

        app(InstallNpmDependencies::class)();
    }

    /** @test */
    function it_installs_npm_dependencies_and_shows_console_output()
    {
        Config::set('lambo.store.node', true);
        Config::set('lambo.store.with_output', true);

        $this->shell->shouldReceive('execInProject')
            ->with("npm install")
            ->once()
            ->andReturn(FakeProcess::success());

        app(InstallNpmDependencies::class)();
    }

    /** @test */
    function it_throws_an_exception_if_npm_install_fails()
    {
        Config::set('lambo.store.node', true);
        Config::set('lambo.store.with_output', false);

        $this->shell->shouldReceive('execInProject')
            ->with('npm install --silent')
            ->once()
            ->andReturn(FakeProcess::fail('npm install --silent'));

        $this->expectException(LamboException::class);

        app(InstallNpmDependencies::class)();
    }
}
