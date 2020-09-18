<?php

namespace Tests\Unit\App\Tools;

use App\Tools\Database;
use App\Tools\DatabaseConnection;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    /** @test */
    function it_detects_mysql()
    {
        $this->mock(DatabaseConnection::class)
            ->shouldReceive('testConnection')
            ->with('mysql', 'host', 'user', 'password', 1234)
            ->andReturnTrue();

        $this->assertTrue(
            app(Database::class)
                ->url("mysql://user:password@host:1234")
                ->find()
        );
    }

    /** @test */
    function it_detects_a_specific_mysql_schema()
    {
        $this->mock(DatabaseConnection::class)
            ->shouldReceive('testConnection')
            ->with('mysql', 'host', 'user', 'password', 1234, 'my-database')
            ->andReturnTrue();

        $this->assertTrue(
            app(Database::class)
                ->url("mysql://user:password@host:1234")
                ->find('my-database')
        );
    }

    /** @test */
    function it_creates_a_mysql_database()
    {
        $this->mock(DatabaseConnection::class)
            ->shouldReceive('createSchema')
            ->with('mysql', 'host', 'user', 'password', 1234, 'my-project')
            ->andReturnTrue();

        $this->assertTrue(
            app(Database::class)
                ->url("mysql://user:password@host:1234")
                ->createSchema('my-project')
        );
    }

    /** @test */
    function it_detects_postgresql()
    {
        $this->mock(DatabaseConnection::class)
            ->shouldReceive('testConnection')
            ->with('pgsql', 'host', 'user', 'password', 1234)
            ->andReturnTrue();

        $this->assertTrue(
            app(Database::class)
                ->url("pgsql://user:password@host:1234")
                ->find()
        );
    }

    /** @test */
    function it_creates_a_postgresql_database()
    {
        $this->mock(DatabaseConnection::class)
            ->shouldReceive('createSchema')
            ->with('pgsql', 'host', 'user', 'password', 1234, 'my-project')
            ->andReturnTrue();

        $this->assertTrue(
            app(Database::class)
                ->url("pgsql://user:password@host:1234")
                ->createSchema('my-project')
        );
    }
}
