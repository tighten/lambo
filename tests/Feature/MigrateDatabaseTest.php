<?php

namespace Tests\Feature;

use App\Actions\MigrateDatabase;
use App\LamboException;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class MigrateDatabaseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    function it_migrates_the_database()
    {
        $this->shell->shouldReceive('execInProject')
            ->with('php artisan migrate --quiet')
            ->once()
            ->andReturn(FakeProcess::success());

        app(MigrateDatabase::class)();
    }

    /** @test */
    function it_throws_an_exception_if_database_migration_fails()
    {
        $this->shell->shouldReceive('execInProject')
            ->with('php artisan migrate --quiet')
            ->once()
            ->andReturn(FakeProcess::fail('php artisan key:generate'));

        $this->expectException(LamboException::class);

        app(MigrateDatabase::class)();
    }
}
