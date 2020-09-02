<?php

namespace Tests\Feature;

use App\Actions\ConfigureFrontendFramework;
use App\Actions\Jetstream;
use App\LamboException;
use App\Shell;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class ConfigureFrontendFrameworkTest extends TestCase
{
    protected $jetstream;

    public function setUp(): void
    {
        $this->markTestSkipped('*** TODO ***');
        parent::setUp();
        $this->jetstream = $this->mock(Jetstream::class);
    }

    /** @test */
    function it_installs_inertia()
    {
        $this->shouldInstallFramework('inertia');
        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_installs_livewire()
    {
        $this->shouldInstallFramework('livewire');
        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_installs_team_features()
    {
        $this->shouldInstallFrameworkWithTeams('inertia');
        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_skips_frontend_framework_installation()
    {
        $shell = $this->spy(Shell::class);
        $laravelUi = $this->spy(Jetstream::class);

        $this->assertEmpty(config('lambo.store.frontend'));

        app(ConfigureFrontendFramework::class);

        $laravelUi->shouldNotHaveReceived('install');
        $shell->shouldNotHaveReceived('execInProject');
    }

    /** @test */
    function it_throws_a_lambo_exception_if_ui_framework_installation_fails()
    {
        $this->shouldFailFrontendFrameworkInstallation('inertia');

        $this->expectException(LamboException::class);

        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_installs_the_framework_with_verbose_output()
    {
        config(['lambo.store.frontend' => 'inertia']);
        config(['lambo.store.with_output' => true]);

        $this->shouldInstallFramework('inertia', false, true, true);

        app(ConfigureFrontendFramework::class)();
    }

    private function shouldFailFrontendFrameworkInstallation(string $frontendFramework)
    {
        $this->shouldInstallFramework($frontendFramework, false, false);
    }

    private function shouldInstallFramework(string $frontendFramework, bool $withTeams = false, bool $success = true, bool $withOutput = false): void
    {
        config(['lambo.store.frontend' => $frontendFramework]);
        config(['lambo.store.with_teams' => $withTeams]);

        $command = sprintf("php artisan jetstream:install %s%s%s",
            $frontendFramework,
            $withTeams ? ' --teams' : '',
            $withOutput ? '' : ' --quiet');

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

    private function shouldInstallFrameworkWithTeams(string $framework)
    {
        $this->shouldInstallFramework($framework, true);
    }
}
