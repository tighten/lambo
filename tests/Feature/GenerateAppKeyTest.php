<?php

namespace Tests\Feature;

use App\Actions\GenerateAppKey;
use App\Shell\Shell;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class GenerateAppKeyTest extends TestCase
{
    /** @test */
    public function it_generates_a_new_app_key()
    {
        $this->fakeLamboConsole();

        $this->mock(Shell::class, function ($shell) {
            $shell->shouldReceive('execInProject')
                ->with('php artisan key:generate')
                ->once()
                ->andReturn(FakeProcess::success());
        });

        app(GenerateAppKey::class)();
    }

    /** @test */
    public function it_throws_an_exception_if_new_app_key_generation_fails()
    {
        $this->fakeLamboConsole();

        $command = 'php artisan key:generate';
        $this->mock(Shell::class, function ($shell) use ($command){
            $shell->shouldReceive('execInProject')
                ->with($command)
                ->once()
                ->andReturn(FakeProcess::fail($command));
        });

        $this->expectExceptionMessage("Failed to generate application key successfully\n  Failed to run: '{$command}'");

        app(GenerateAppKey::class)();
    }
}
