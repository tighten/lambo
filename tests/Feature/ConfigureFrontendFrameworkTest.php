<?php

namespace Tests\Feature;

use App\Actions\ConfigureFrontendFramework;
use App\Actions\LaravelUi;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class ConfigureFrontendFrameworkTest extends TestCase
{
    /** @test */
    function it_installs_the_specified_front_end_framework()
    {
        $shell = $this->mock(Shell::class);
        $laravelUi = $this->mock(LaravelUi::class);

        Config::set('lambo.store.frontend', 'foo-frontend');

        $laravelUi->shouldReceive('install')
            ->once()
            ->globally()
            ->ordered();

        $shell->shouldReceive('execInProject')
            ->with('php artisan ui foo-frontend')
            ->once()
            ->andReturn(FakeProcess::success())
            ->globally()
            ->ordered();

        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_does_not_install_a_frontend_framework_when_none_is_specified()
    {
        $shell = $this->spy(Shell::class);
        $laravelUi = $this->spy(LaravelUi::class);

        $this->assertEmpty(Config::get('lambo.store.frontend'));

        app(ConfigureFrontendFramework::class);

        $laravelUi->shouldNotHaveReceived('install');
        $shell->shouldNotHaveReceived('execInProject');
    }

    /** @test */
    function it_throws_and_exception_if_the_ui_framework_installation_fails()
    {
        $shell = $this->mock(Shell::class);
        $laravelUi = $this->mock(LaravelUi::class);

        Config::set('lambo.store.frontend', 'foo-frontend');

        $laravelUi->shouldReceive('install')
            ->once()
            ->globally()
            ->ordered();

        $command = 'php artisan ui foo-frontend';
        $shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command))
            ->globally()
            ->ordered();

        $this->expectException(Exception::class);

        app(ConfigureFrontendFramework::class)();
    }
}
