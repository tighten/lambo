<?php

namespace Tests\Feature;

use App\Actions\GenerateAppKey;
use App\LamboException;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class GenerateAppKeyTest extends TestCase
{
    /** @test */
    function it_generates_a_new_app_key()
    {
        $this->shell->shouldReceive('execInProject')
            ->with('php artisan key:generate --quiet')
            ->once()
            ->andReturn(FakeProcess::success());

        app(GenerateAppKey::class)();
    }

    /** @test */
    function it_throws_an_exception_if_new_app_key_generation_fails()
    {
        $this->shell->shouldReceive('execInProject')
            ->with('php artisan key:generate --quiet')
            ->once()
            ->andReturn(FakeProcess::fail('php artisan key:generate'));

        $this->expectException(LamboException::class);

        app(GenerateAppKey::class)();
    }
}
