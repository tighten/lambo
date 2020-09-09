<?php

namespace Tests\Feature;

use App\Actions\RunLaravelInstaller;
use App\ConsoleWriter;
use App\LamboException;
use App\Shell;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class RunLaravelInstallerTest extends TestCase
{
    /** @test */
    function it_runs_the_laravel_installer()
    {
        collect([
            [
                'command' => 'laravel new my-project --quiet',
                'lambo.store.dev' => false,
                'lambo.store.with_output' => false,
            ],
            [
                'command' => 'laravel new my-project',
                'lambo.store.dev' => false,
                'lambo.store.with_output' => true,
            ],
            [
                'command' => 'laravel new my-project --dev --quiet',
                'lambo.store.dev' => true,
                'lambo.store.with_output' => false,
            ],
            [
                'command' => 'laravel new my-project --dev',
                'lambo.store.dev' => true,
                'lambo.store.with_output' => true,
            ],
        ])->each(function ($options) {
            config(['lambo.store.project_name' => 'my-project']);
            config(['lambo.store.dev' => $options['lambo.store.dev']]);
            config(['lambo.store.with_output' => $options['lambo.store.with_output']]);

            $this->shell->shouldReceive('execInRoot')
                ->with($options['command'])
                ->once()
                ->andReturn(FakeProcess::success());

            app(RunLaravelInstaller::class)();
        });
    }

    /** @test */
    function it_throws_an_exception_if_the_laravel_installer_fails()
    {
        config(['lambo.store.project_name' => 'my-project']);
        config(['lambo.store.dev' => false]);
        config(['lambo.store.with_output' => false]);

        $this->shell->shouldReceive('execInRoot')
            ->andReturn(FakeProcess::fail('failed command'));

        $this->expectException(LamboException::class);

        app(RunLaravelInstaller::class)();
    }
}
