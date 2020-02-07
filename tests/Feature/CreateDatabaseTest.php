<?php

namespace Tests\Feature;

use App\Actions\CreateDatabase;
use App\Shell\Shell;
use Illuminate\Support\Facades\Config;
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
        $this->assertEquals('Created a new database ' . Config::get('lambo.store.database_name'), app(CreateDatabase::class)());
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

        $this->assertEquals('MySql does not seem to be installed. Skipping new database creation.', app(CreateDatabase::class)());
    }

    /** @test */
    public function it_replaces_hyphens_with_underscores_in_database_names()
    {
        $this->fakeLamboConsole();

        Config::set('lambo.store.create_database', true);
        Config::set('lambo.store.database_username', 'user');
        Config::set('lambo.store.database_password', 'password');
        Config::set('lambo.store.database_name', 'name-to-change');

        $this->mock(Shell::class, function ($shell) {
            $shell->shouldReceive('execInProject')
                ->with('mysql --user=user --password=password -e "CREATE DATABASE IF NOT EXISTS name_to_change";')
                ->once()
                ->andReturn(FakeProcess::success());
        });
        $this->assertEquals('Created a new database name_to_change', app(CreateDatabase::class)());
    }

    /** @test */
    public function it_throws_an_exception_if_database_creation_fails()
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

        $this->expectExceptionMessage('The new database was not created.' . PHP_EOL . "  Failed to run: '{$command}'");

        app(CreateDatabase::class)();
    }
}
