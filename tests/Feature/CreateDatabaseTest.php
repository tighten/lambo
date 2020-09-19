<?php

namespace Tests\Feature;

use App\Actions\CreateDatabase;
use App\Tools\Database;
use Tests\TestCase;

class CreateDatabaseTest extends TestCase
{
    private $database;

    public function setUp(): void
    {
        parent::setUp();
        $this->database = $this->mock(Database::class);
    }

    /** @test */
    function it_creates_a_mysql_database()
    {
        $this->database->shouldReceive('url')
            ->with('mysql://user:password@example.test:3306')
            ->once()
            ->andReturnSelf();

        $this->database->shouldReceive('find')
            ->once()
            ->andReturnTrue();

        $this->database->shouldReceive('url')
            ->with('mysql://user:password@example.test:3306')
            ->once()
            ->andReturnSelf();

        $this->database->shouldReceive('createSchema')
            ->once()
            ->globally()
            ->andReturnTrue()
            ->ordered();

        config(['lambo.store.create_database' => true]);

        config(['lambo.store.database_host' => 'example.test']);
        config(['lambo.store.database_port' => 3306]);
        config(['lambo.store.database_username' => 'user']);
        config(['lambo.store.database_password' => 'password']);
        config(['lambo.store.database_name' => 'foo']);

        app(CreateDatabase::class)();
    }

    /** @test */
    function it_skips_database_creation()
    {
        $spy = $this->spy(Database::class);

        config(['lambo.store.create_database' => false]);

        config(['lambo.store.database_host' => 'example.test']);
        config(['lambo.store.database_port' => 3306]);
        config(['lambo.store.database_username' => 'user']);
        config(['lambo.store.database_password' => 'password']);
        config(['lambo.store.database_name' => 'foo']);

        app(CreateDatabase::class)();

        $spy->shouldNotHaveReceived('find');
        $spy->shouldNotHaveReceived('createSchema');
    }
}
