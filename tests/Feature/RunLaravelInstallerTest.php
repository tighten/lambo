<?php

namespace Tests\Feature;

use App\Actions\RunLaravelInstaller;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class RunLaravelInstallerTest extends TestCase
{

    /** @test */
    public function it_runs_the_laravel_installer()
    {
        $this->fakeLamboConsole();

        collect([
            [
                'command' => 'laravel new my-project --quiet',
                'lambo.store.auth' => false,
                'lambo.store.dev' => false,
                'lambo.store.with_output' => false,
            ],
            [
                'command' => 'laravel new my-project',
                'lambo.store.auth' => false,
                'lambo.store.dev' => false,
                'lambo.store.with_output' => true,
            ],
            [
                'command' => 'laravel new my-project --dev --quiet',
                'lambo.store.auth' => false,
                'lambo.store.dev' => true,
                'lambo.store.with_output' => false,
            ],
            [
                'command' => 'laravel new my-project --dev',
                'lambo.store.auth' => false,
                'lambo.store.dev' => true,
                'lambo.store.with_output' => true,
            ],
            [
                'command' => 'laravel new my-project --auth --quiet',
                'lambo.store.auth' => true,
                'lambo.store.dev' => false,
                'lambo.store.with_output' => false,
            ],
            [
                'command' => 'laravel new my-project --auth',
                'lambo.store.auth' => true,
                'lambo.store.dev' => false,
                'lambo.store.with_output' => true,
            ],
            [
                'command' => 'laravel new my-project --auth --dev --quiet',
                'lambo.store.auth' => true,
                'lambo.store.dev' => true,
                'lambo.store.with_output' => false,
            ],

            [
                'command' => 'laravel new my-project --auth --dev',
                'lambo.store.auth' => true,
                'lambo.store.dev' => true,
                'lambo.store.with_output' => true,
            ],
        ])->each(function($options){
            Config::set('lambo.store.project_name', 'my-project');
            Config::set('lambo.store.auth', $options['lambo.store.auth']);
            Config::set('lambo.store.dev', $options['lambo.store.dev']);
            Config::set('lambo.store.with_output', $options['lambo.store.with_output']);
            $this->runLaravelInstaller($options['command']);
        });

    }

    /** @test */
    public function it_throws_an_exception_if_the_laravel_installer_fails()
    {
        $this->fakeLamboConsole();

        $this->mock(Shell::class, function ($shell) {
            $shell->shouldReceive('execInRoot')
                ->andReturn(FakeProcess::failed('failed command'));
        });

        Config::set('lambo.store.project_name', 'my-project');
        Config::set('lambo.store.auth', false);
        Config::set('lambo.store.dev', false);
        Config::set('lambo.store.with_output', false);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The laravel installer did not complete successfully.' . PHP_EOL . "  Failed to run: 'failed command'");

        app(RunLaravelInstaller::class)();
    }

    protected function runLaravelInstaller(string $expectedCommand)
    {
        $this->instance(Shell::class, Mockery::mock(Shell::class, function ($shell) use ($expectedCommand) {
            $shell->shouldReceive('execInRoot')
                ->with($expectedCommand)
                ->once()
                ->andReturn(FakeProcess::successful());
        }));

        app(RunLaravelInstaller::class)();
    }
}
