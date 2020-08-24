<?php

namespace Tests\Feature;

use App\Actions\ConfigureFrontendFramework;
use App\Actions\LaravelUi;
use App\LamboException;
use App\Shell;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class ConfigureFrontendFrameworkTest extends TestCase
{
    private $shell;
    private $laravelUi;

    public function setUp(): void
    {
        parent::setUp();
        $this->shell = $this->mock(Shell::class);
        $this->laravelUi = $this->mock(LaravelUi::class);
    }

    /** @test */
    function it_installs_vue()
    {
        $this->shouldInstallFrontendFramework('vue');
        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_installs_react()
    {
        $this->shouldInstallFrontendFramework('react');
        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_installs_bootstrap()
    {
        $this->shouldInstallFrontendFramework('bootstrap');
        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_skips_frontend_framework_installation()
    {
        $shell = $this->spy(Shell::class);
        $laravelUi = $this->spy(LaravelUi::class);

        $this->assertEmpty(config('lambo.store.frontend'));

        app(ConfigureFrontendFramework::class);

        $laravelUi->shouldNotHaveReceived('install');
        $shell->shouldNotHaveReceived('execInProject');
    }

    /** @test */
    function it_throws_a_lambo_exception_if_ui_framework_installation_fails()
    {
        $this->shouldFailFrontendFrameworkInstallation('vue');

        $this->expectException(LamboException::class);

        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_installs_the_framework_with_verbose_output()
    {
        config(['lambo.store.frontend' => 'vue']);
        config(['lambo.store.with_output' => true]);

        $this->shouldInstallFrontendFramework('vue', true, false);

        app(ConfigureFrontendFramework::class)();
    }

    private function shouldFailFrontendFrameworkInstallation(string $frontendFramework)
    {
        $this->shouldInstallFrontendFramework($frontendFramework, false);
    }

    private function shouldInstallFrontendFramework(string $frontendFramework, bool $success = true, bool $quiet = true): void
    {
        config(['lambo.store.frontend' => $frontendFramework]);

        $command = sprintf("php artisan ui %s%s", $frontendFramework, $quiet ? ' --quiet' : '');

        $this->laravelUi->shouldReceive('install')
            ->once()
            ->globally()
            ->ordered();

        $expectation = $this->shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->globally()
            ->ordered();

        if ($success) {
            $expectation->andReturn(FakeProcess::success());
        } else {
            $expectation->andReturn(FakeProcess::fail($command));
        }
    }
}
