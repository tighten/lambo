<?php

namespace Tests\Feature;

use App\Actions\LaravelUi;
use App\LamboException;
use App\Shell;
use Illuminate\Support\Facades\File;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class LaravelUiTest extends TestCase
{
    private $shell;

    public function setUp(): void
    {
        parent::setUp();
        $this->shell = $this->mock(Shell::class);
    }

    /** @test */
    function it_installs_laravel_ui()
    {
        config(['lambo.store.project_path' => '/some/project/path']);

        $composerJsonFixture = File::get(base_path('tests/Feature/Fixtures/composer-without-laravel-ui.json'));

        File::shouldReceive('get')
            ->with('/some/project/path/composer.json')
            ->once()
            ->andReturn($composerJsonFixture)
            ->globally()
            ->ordered();

        $this->shell->shouldReceive('execInProject')
            ->with('composer require laravel/ui --quiet')
            ->once()
            ->andReturn(FakeProcess::success());

        app(LaravelUi::class)->install();
    }

    /** @test */
    function it_skips_laravel_ui_installation_if_it_is_already_present()
    {
        $shell = $this->spy(Shell::class);

        config(['lambo.store.project_path' => '/some/project/path']);

        $composerJsonFixture = File::get(base_path('tests/Feature/Fixtures/composer-with-laravel-ui.json'));

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

        config(['lambo.store.project_path' => '/some/project/path']);

        $this->shell->shouldReceive('execInProject')
            ->with('composer require laravel/ui --quiet')
            ->once()
            ->andReturn(FakeProcess::fail('composer require laravel/ui --quiet'));

        $this->expectException(LamboException::class);

        app(LaravelUi::class)->install();
    }
}
