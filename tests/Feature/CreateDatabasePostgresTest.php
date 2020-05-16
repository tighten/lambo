<?php

namespace Tests\Feature;

use App\Actions\CreateDatabase;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\ExecutableFinder;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class CreateDatabasePostgresTest extends TestCase
{
    private $shell;

    function setUp(): void
    {
        if (!$this->postgresExists()) {
            $this->markTestSkipped('Postgres is a dependency of this test but cannot be found.');
        }

        parent::setUp();
        $this->shell = $this->mock(Shell::class);
    }

    /** @test */
    function it_creates_a_postgres_database()
    {
        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_type', 'pgsql');
        Config::set('lambo.store.database_name', 'database_name');

        $this->shell->shouldReceive('execInProject')
            ->with('createdb database_name')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->assertStringContainsString(Config::get('lambo.store.database_name'), app(CreateDatabase::class)());
    }

    /** @test */
    function it_checks_if_postgres_is_installed()
    {
        $executableFinder = $this->mock(ExecutableFinder::class);

        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_type', 'pgsql');

        $executableFinder->shouldReceive('find')
            ->with('psql')
            ->once()
            ->andReturn(null);

        $this->assertEquals('Postgres does not seem to be installed. Skipping new database creation.', app(CreateDatabase::class)());
    }

    /** @test */
    function it_throws_an_exception_if_database_creation_fails()
    {
        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_type', 'pgsql');
        Config::set('lambo.store.database_name', 'database_name');

        $this->shell->shouldReceive('execInProject')
            ->with('createdb database_name')
            ->once()
            ->andReturn(FakeProcess::fail('createdb database_name'));

        $this->expectException(Exception::class);

        app(CreateDatabase::class)();
    }

    function postgresExists()
    {
        return (new ExecutableFinder)->find('psql') !== null;
    }
}
