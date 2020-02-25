<?php

namespace Tests\Feature;

use App\Actions\GenerateAppKey;
use App\Shell\Shell;
use Exception;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class GenerateAppKeyTest extends TestCase
{
    /** @test */
    function it_generates_a_new_app_key()
    {
        $shell = $this->mock(Shell::class);

        $shell->shouldReceive('execInProject')
            ->with('php artisan key:generate')
            ->once()
            ->andReturn(FakeProcess::success());

        app(GenerateAppKey::class)();
    }

    /** @test */
    function it_throws_an_exception_if_new_app_key_generation_fails()
    {
        $shell = $this->mock(Shell::class);

        $shell->shouldReceive('execInProject')
            ->with('php artisan key:generate')
            ->once()
            ->andReturn(FakeProcess::fail('php artisan key:generate'));

        $this->expectException(Exception::class);

        app(GenerateAppKey::class)();
    }
}
