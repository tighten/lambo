<?php

namespace Tests\Feature;

use App\Actions\CreateDatabase;
use App\LamboException;
use App\Shell;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\ExecutableFinder;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class CreateDatabaseTest extends TestCase
{
    private $shell;

    function setUp(): void
    {
        if (! $this->mysqlExists()) {
            $this->markTestSkipped('MySQL is a dependency of this test but cannot be found.');
        }

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

        $this->shell->shouldReceive('exec')
            ->with('mysql.server status')
            ->once()
            ->andReturn(FakeProcess::success()->withOutput('MySQL running'));

        $this->shell->shouldReceive('exec')
            ->with('mysql --user=user --password=password -e "CREATE DATABASE IF NOT EXISTS database_name";')
            ->once()
            ->andReturn(FakeProcess::success());

        app(CreateDatabase::class)();
    }

    /** @test */
    function it_throws_an_exception_if_mysql_is_not_installed()
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

        $this->expectException(LamboException::class);

        app(CreateDatabase::class)();
    }

    /** @test */
    function it_throws_an_exception_if_database_creation_fails()
    {
        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_username', 'user');
        Config::set('lambo.store.database_password', 'password');
        Config::set('lambo.store.database_name', 'database_name');

        $this->shell->shouldReceive('exec')
            ->with('mysql.server status')
            ->once()
            ->andReturn(FakeProcess::success()->withOutput('MySQL running'));

        $this->shell->shouldReceive('exec')
            ->with('mysql --user=user --password=password -e "CREATE DATABASE IF NOT EXISTS database_name";')
            ->once()
            ->andReturn(FakeProcess::fail('mysql --user=user --password=password -e "CREATE DATABASE IF NOT EXISTS database_name";'));

        $this->expectException(LamboException::class);

        app(CreateDatabase::class)();
    }

    function mysqlExists()
    {
        return (new ExecutableFinder)->find('mysql') !== null;
    }
}
