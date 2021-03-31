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
        $fakeStore = [
            'create_database' => true,
            'database_host' => 'example.test',
            'database_port' => 3306,
            'database_username' => 'user',
            'database_password' => 'password',
            'database_name' => 'foo',
        ];

        $this->database->shouldReceive('fillFromLamboStore')
            ->with($fakeStore)
            ->once()
            ->andReturnSelf();

        $this->database->shouldReceive('create')
            ->once()
            ->globally()
            ->andReturnTrue()
            ->ordered();

        config(['lambo.store' => $fakeStore]);

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

    /** @test */
    function it_registers_a_lambo_summary_warning_if_execution_fails()
    {
        $this->markTestIncomplete('[ Incomplete ] It registers a lambo summary warning if execution fails');
    }
}
