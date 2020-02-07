<?php

namespace Tests\Feature;

use App\Actions\CreateDatabase;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Mockery;
use Symfony\Component\Process\ExecutableFinder;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class CreateDatabaseTest extends TestCase
{
    /** @test */
    function it_creates_a_mysql_database()
    {
        $this->fakeLamboConsole();

        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_username', 'user');
        Config::set('lambo.store.database_password', 'password');
        Config::set('lambo.store.database_name', 'database_name');

        $this->mock(Shell::class, function ($shell) {
            $shell->shouldReceive('execInProject')
                ->with('mysql --user=user --password=password -e "CREATE DATABASE IF NOT EXISTS database_name";')
                ->once()
                ->andReturn(FakeProcess::success());
        });
        $this->assertStringContainsString(Config::get('lambo.store.database_name'), app(CreateDatabase::class)());
    }

    /** @test */
    function it_checks_if_mysql_is_installed()
    {
        $this->fakeLamboConsole();

        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_username', 'user');
        Config::set('lambo.store.database_password', 'password');
        Config::set('lambo.store.database_name', 'database_name');

        $this->mock(ExecutableFinder::class, function($executableFinder){
            $executableFinder->shouldReceive('find')
                ->with('mysql')
                ->once()
                ->andReturn(null);
        });

        $this->assertEquals('MySQL does not seem to be installed. Skipping new database creation.', app(CreateDatabase::class)());
    }

    /** @test */
    function it_throws_an_exception_if_database_creation_fails()
    {
        $this->fakeLamboConsole();

        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_username', 'user');
        Config::set('lambo.store.database_password', 'password');
        Config::set('lambo.store.database_name', 'database_name');

        $command = 'mysql --user=user --password=password -e "CREATE DATABASE IF NOT EXISTS database_name";';
        $this->mock(Shell::class, function ($shell) use ($command){
            $shell->shouldReceive('execInProject')
                ->with($command)
                ->once()
                ->andReturn(FakeProcess::fail($command));
        });

        $this->expectException(Exception::class);

        app(CreateDatabase::class)();
    }
}
