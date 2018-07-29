<?php

namespace App\Actions;

use App\Support\BaseAction;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends BaseAction
{
    public function __invoke()
    {
        $lamboDatabase = config('lambo.database');

        if ($lamboDatabase === false) {
            return;
        }

        if ($lamboDatabase === 'mysql') {
            $this->setDatabaseHostConfigs();
            $this->createMySQLDatabase();
        }

        if ($lamboDatabase === 'sqlite') {
            $this->createSqliteDatabase();
        }
    }

    /**
     * Set database host configuration.
     *
     */
    protected function setDatabaseHostConfigs(): void
    {
        $hostDbKeyPrefix = 'database.connections.host_database';

        config()->set("{$hostDbKeyPrefix}.host", config('lambo.db_host'));
        config()->set("{$hostDbKeyPrefix}.port", config('lambo.db_port'));
        config()->set("{$hostDbKeyPrefix}.username", config('lambo.db_username'));
        config()->set("{$hostDbKeyPrefix}.password", config('lambo.db_password'));
    }

    /**
     * Creates the MySQL Database
     *
     */
    protected function createMySQLDatabase():void
    {
        try {
            $this->console->alert('Creating database...');

            $connection = DB::connection('host_database');

            $dbName = config('lambo-store.db_name');

            $databases = $connection->select('SHOW DATABASES');

            $exists = collect($databases)->filter(function ($item, $key) use ($dbName) {
                return $item->Database === $dbName;
            })->count();

            if ($exists) {
                $this->console->error('Database already existed! It was left as we found it.');
                return;
            }

            $connection->statement("CREATE DATABASE IF NOT EXISTS {$dbName}");

            $databases = $connection->select('SHOW DATABASES');

            $checkExists = collect($databases)->filter(function ($item, $key) use ($dbName) {
                return $item->Database === $dbName;
            })->count();

            if ($checkExists) {
                $this->console->info('Database successfully created');
                return;
            }

            $this->console->error('Could not create database!');
        } catch (\Exception $exception) {
            $this->console->error("Error creating database: {$exception->getMessage()}");
        }
    }

    /**
     * Creates the Sqlite Database
     *
     */
    protected function createSqliteDatabase():void
    {
        $this->console->info('Creating sqlite file.');
        $this->shell->inDirectory(config('lambo-store.project_path'), 'touch database/database.sqlite');
    }
}
