<?php

namespace Tests\Unit\App\Tools;

use App\Shell;
use App\Tools\Takeout;
use Symfony\Component\Process\ExecutableFinder;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class TakeoutTest extends TestCase
{
    /** @test */
    function it_detects_takeout()
    {
        $this->mock(ExecutableFinder::class)
            ->shouldReceive('find')
            ->with('takeout')
            ->andReturn('/path/to/takeout');

        $takeout = app(Takeout::class);

        $this->assertTrue(
            $takeout->exists()
        );
    }

    /** @test */
    function it_lists_containers()
    {
        $json = '[{"container_id":"541cc3717d1b","names":"TO--memcached--1.6.6","status":"Exited (255) 13 days ago","ports":"11211\/tcp, 0.0.0.0:11211->11212\/tcp"},{"container_id":"49667b50ca07","names":"TO--mysql--5.7","status":"Exited (255) 4 hours ago","ports":"33060\/tcp, 0.0.0.0:3357->3306\/tcp"},{"container_id":"1e74b4ae49f8","names":"TO--mysql--8","status":"Exited (0) 44 hours ago","ports":""}]';
        $this->mock(Shell::class)
            ->shouldReceive('execQuietly')
            ->with('takeout list --json')
            ->andReturn(FakeProcess::success()->withOutput($json));

        $takeout = app(Takeout::class);

        $this->assertEquals(
            json_decode($json, true),
            $takeout->list()
        );
    }

    /** @test */
    function it_lists_containers_of_a_given_type()
    {
        $allContainersJson = '[
            {"container_id":"????????????","names":"To--mysql--8","status":"????","ports":"????"},
            {"container_id":"????????????","names":"To--mysql--5.7","status":"????","ports":"????"},
            {"container_id":"????????????","names":"To--postgresql--13.0","status":"????","ports":"????"},
            {"container_id":"????????????","names":"To--beanstalkd--????","status":"????","ports":"????"},
            {"container_id":"????????????","names":"To--mailhog--????","status":"????","ports":"????"}
        ]';

        $filteredContainersJson = '[
            {"container_id":"????????????","names":"To--mysql--8","status":"????","ports":"????"},
            {"container_id":"????????????","names":"To--mysql--5.7","status":"????","ports":"????"},
            {"container_id":"????????????","names":"To--postgresql--13.0","status":"????","ports":"????"}
        ]';

        $this->mock(Shell::class)
            ->shouldReceive('execQuietly')
            ->with('takeout list --json')
            ->andReturn(FakeProcess::success()->withOutput($allContainersJson));

        $this->assertEquals(
            json_decode($filteredContainersJson, true),
            app(Takeout::class)->only(['mysql', 'postgresql'])->list()
        );
    }

    /** @test */
    function it_returns_true_when_the_container_starts_successfully()
    {
        $this->mock(Shell::class)
            ->shouldReceive('execQuietly')
            ->with('takeout start someContainer')
            ->andReturn(FakeProcess::success());

        $this->assertEquals(
            true,
            (app(Takeout::class))->start('someContainer')
        );
    }

    /** @test */
    function it_returns_false_when_the_container_fails_to_start()
    {
        $command = 'takeout start someContainer';
        $this->mock(Shell::class)
            ->shouldReceive('execQuietly')
            ->with($command)
            ->andReturn(FakeProcess::fail($command));

        $this->assertEquals(
            false,
            (app(Takeout::class))->start('someContainer')
        );
    }
}
