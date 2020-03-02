<?php

namespace Tests\Feature;

use App\Actions\CreateDatabase;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\ExecutableFinder;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class CreateDatabaseTest extends TestCase
{
    private $shell;

    public function setUp(): void
    {
        parent::setUp();
        $this->shell = $this->mock(Shell::class);
    }

    /** @test */
    function it_creates_a_mysql_database()
    {
        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_username', 'user');
        Config::set('lambo.store.database_password', 'password');
        Config::set('lambo.store.database_name', 'database_name');

        $this->shell->shouldReceive('execInProject')
            ->with('mysql --user=user --password=password -e "CREATE DATABASE IF NOT EXISTS database_name";')
            ->once()
            ->andReturn(FakeProcess::success());

        $this->assertStringContainsString(Config::get('lambo.store.database_name'), app(CreateDatabase::class)());
    }

    /** @test */
    function it_checks_if_mysql_is_installed()
    {
        $executableFinder = $this->mock(ExecutableFinder::class);

        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_username', 'user');
        Config::set('lambo.store.database_password', 'password');
        Config::set('lambo.store.database_name', 'database_name');

        $executableFinder->shouldReceive('find')
            ->with('mysql')
            ->once()
            ->andReturn(null);

        $this->assertEquals('MySQL does not seem to be installed. Skipping new database creation.', app(CreateDatabase::class)());
    }

    /**
     * @todo do we need to test that database creation only happens when MySQL is the configured database?
     * @test
     */
    function it_only_runs_when_mysql_is_the_configured_database()
    {
        $this->markTestSkipped('*** @TODO: Add Test: "it only runs when mysql is the configured database" ***');
    }

    /** @test */
    function it_throws_an_exception_if_database_creation_fails()
    {
        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_username', 'user');
        Config::set('lambo.store.database_password', 'password');
        Config::set('lambo.store.database_name', 'database_name');

        $this->shell->shouldReceive('execInProject')
            ->with('mysql --user=user --password=password -e "CREATE DATABASE IF NOT EXISTS database_name";')
            ->once()
            ->andReturn(FakeProcess::fail('mysql --user=user --password=password -e "CREATE DATABASE IF NOT EXISTS database_name";'));

        $this->expectException(Exception::class);

        app(CreateDatabase::class)();
    }
}
