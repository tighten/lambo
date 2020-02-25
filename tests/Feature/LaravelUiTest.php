<?php

namespace Tests\Feature;

use App\Actions\LaravelUi;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class LaravelUiTest extends TestCase
{
    /** @test */
    function it_installs_laravel_ui()
    {
        $shell = $this->mock(Shell::class);

        $composerJsonFixture = File::get(base_path('tests/Feature/Fixtures/composer-without-laravel-ui.json'));

        Config::set('lambo.store.project_path', '/some/project/path');

        File::shouldReceive('get')
            ->with('/some/project/path/composer.json')
            ->once()
            ->andReturn($composerJsonFixture)
            ->globally()
            ->ordered();

        $shell->shouldReceive('execInProject')
            ->with('composer require laravel/ui --quiet')
            ->once()
            ->andReturn(FakeProcess::success());

        app(LaravelUi::class)->install();
    }

    /** @test */
    function it_does_not_install_laravel_ui_if_it_is_already_present()
    {
        $shell = $this->spy(Shell::class);

        $composerJsonFixture = File::get(base_path('tests/Feature/Fixtures/composer-with-laravel-ui.json'));

        Config::set('lambo.store.project_path', '/some/project/path');

        File::shouldReceive('get')
            ->with('/some/project/path/composer.json')
            ->once()
            ->andReturn($composerJsonFixture)
            ->globally()
            ->ordered();

        app(LaravelUi::class)->install();

        $shell->shouldNotHaveReceived('execInProject');
    }

    /** @test */
    function it_throws_an_exception_if_laravel_ui_fails_to_install()
    {
        File::shouldReceive('get');
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.project_path', '/some/project/path');

        $command = 'composer require laravel/ui --quiet';
        $shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->andReturn(FakeProcess::fail($command));

        $this->expectException(Exception::class);

        app(LaravelUi::class)->install();
    }
}
