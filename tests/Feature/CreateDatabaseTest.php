<?php

namespace Tests\Feature;

use App\Actions\CreateDatabase;
use App\LamboException;
use Symfony\Component\Process\ExecutableFinder;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class CreateDatabaseTest extends TestCase
{
    /** @test */
    function it_creates_a_mysql_database()
    {
        $this->mock(ExecutableFinder::class)
            ->shouldReceive('find')
            ->with('mysql')
            ->once()
            ->andReturn('/path/to/mysql');

        config(['lambo.store.create_database' => true]);
        config(['lambo.store.database_username' => 'user']);
        config(['lambo.store.database_password' => 'password']);
        config(['lambo.store.database_name' => 'database_name']);

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
    function it_throws_an_exception_if_database_creation_fails()
    {
        $this->mock(ExecutableFinder::class)
            ->shouldReceive('find')
            ->with('mysql')
            ->once()
            ->andReturn('/path/to/mysql');

        config(['lambo.store.create_database' => true]);
        config(['lambo.store.database_username' => 'user']);
        config(['lambo.store.database_password' => 'password']);
        config(['lambo.store.database_name' => 'database_name']);

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
