<?php

namespace Tests\Feature;

use App\Actions\ConfigureFrontendFramework;
use App\LamboException;
use App\Shell;
use Illuminate\Support\Facades\File;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class ConfigureFrontendFrameworkTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        config(['lambo.store.project_path' => base_path('tests/Feature/Fixtures')]);
    }

    /** @test */
    function it_installs_laravel_jetstream()
    {
        $this->skipWithMessage([
            'Currently failing due to WIP refactor.',
            'Mock expectations are not being specified correctly.',
        ]);

        config(['lambo.store.project_path' => '/some/project/path']);

        $this->composerMissing();

        $this->shell->shouldReceive('execInProject')
            ->with('composer require laravel/jetstream --quiet')
            ->once()
            ->andReturn(FakeProcess::success())
            ->globally()
            ->ordered();

        $this->shouldInstallFramework('inertia');

        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_laravel_jetstream_fails_to_install()
    {
        config(['lambo.store.project_path' => '/some/project/path']);

        $this->composerMissing();

        $this->shell->shouldReceive('execInProject')
            ->with('composer require laravel/jetstream --quiet')
            ->once()
            ->andReturn(FakeProcess::fail('composer require laravel/jetstream --quiet'))
            ->globally()
            ->ordered();

        config(['lambo.store.frontend' => 'inertia']);

        $this->expectException(LamboException::class);

        app(ConfigureFrontendFramework::class)();
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

        $this->assertEmpty(config('lambo.store.frontend'));

        app(ConfigureFrontendFramework::class);

        $shell->shouldNotHaveReceived('execInProject');
    }

    /** @test */
    function it_throws_a_lambo_exception_if_ui_framework_installation_fails()
    {
        // '@todo < *** FIGURE THIS OUT *** >'
        $this->skipWithMessage([
            'Currently failing due to WIP refactor.',
            'the App\Shell mock needs to return both a successful and a failed',
            'process execution.'
        ]);
        $this->shouldFailFrontendFrameworkInstallation('inertia');

        $this->expectException(LamboException::class);

        app(ConfigureFrontendFramework::class)();
    }

    /** @test */
    function it_installs_the_framework_with_verbose_output()
    {
        $this->skipWithMessage([
            'Currently failing due to WIP refactor.',
            'Mock expectations are not being specified correctly.',
        ]);

        config(['lambo.store.frontend' => 'inertia']);
        config(['lambo.store.with_output' => true]);

        $this->shouldInstallFramework('inertia', false, true, true);

        app(ConfigureFrontendFramework::class)();
    }

    protected function composerMissing(): void
    {
        $composerJsonFixture = File::get(base_path('tests/Feature/Fixtures/composer-without-laravel-jetstream.json'));

        File::shouldReceive('get')
            ->with('/some/project/path/composer.json')
            ->once()
            ->andReturn($composerJsonFixture)
            ->globally()
            ->ordered();
    }

    private function shouldFailFrontendFrameworkInstallation(string $frontendFramework)
    {
        $this->shouldInstallFramework($frontendFramework, false, false);
    }

    private function shouldInstallFramework(string $frontendFramework, bool $withTeams = false, bool $success = true, bool $withOutput = false): void
    {
        config(['lambo.store.frontend' => $frontendFramework]);
        config(['lambo.store.teams' => $withTeams]);

        $command = sprintf("php artisan jetstream:install %s%s%s",
            $frontendFramework,
            $withTeams ? ' --teams' : '',
            $withOutput ? '' : ' --quiet');

        $expectation = $this->shell->shouldReceive('execInProject')
            ->with($command)
            ->once()
            ->globally()
            ->ordered();

        if ($frontendFramework === 'inertia') {
            $this->shell->shouldReceive('execInProject')
                ->with('npm install --silent')
                ->once()
                ->globally()
                ->ordered()
                ->andReturn(FakeProcess::success());

            $this->shell->shouldReceive('execInProject')
                ->with('npm run dev --silent')
                ->once()
                ->globally()
                ->ordered()
                ->andReturn(FakeProcess::success());
        }

        $this->shell->shouldReceive('execInProject')
            ->with('php artisan migrate --quiet')
            ->once()
            ->globally()
            ->ordered()
            ->andReturn(FakeProcess::success());

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
