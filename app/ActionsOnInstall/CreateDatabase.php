<?php

namespace App\ActionsOnInstall;

use Exception;
use App\Support\BaseAction;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends BaseAction
{
    /**
     * Represent the host database service, without a selected
     * database. This is to be able to create a new database.
     *
     * @var string
     */
    protected $hostDatabase = 'host_database';

    /**
     * Creates the database.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $databaseType = config('lambo.config.database');

        if ($databaseType === false) {
            return;
        }

        if ($databaseType === 'mysql') {
            $this->setDatabaseHostConfigs();
            $this->createMySQLDatabase();
        }

        if ($databaseType === 'sqlite') {
            $this->createSqliteDatabase();
        }
    }

    /**
     * Set database host configuration.
     *
     * @return void
     */
    protected function setDatabaseHostConfigs(): void
    {
        $hostDbKeyPrefix = "database.connections.{$this->hostDatabase}";

        config()->set("{$hostDbKeyPrefix}.host", config('lambo.config.db_host'));
        config()->set("{$hostDbKeyPrefix}.port", config('lambo.config.db_port'));
        config()->set("{$hostDbKeyPrefix}.username", config('lambo.config.db_username'));
        config()->set("{$hostDbKeyPrefix}.password", config('lambo.config.db_password'));
    }

    /**
     * Creates the MySQL Database
     *
     * @return void
     */
    protected function createMySQLDatabase(): void
    {
        try {
            $this->console->info('Creating database...');

            $connection = DB::connection($this->hostDatabase);

            $dbName = config('lambo.store.db_name');

            $databases = $connection->select('SHOW DATABASES');

            $matchingCount = collect($databases)->where('Database', $dbName)->count();

            if ($matchingCount > 0) {
                $this->console->alert('Database already existed! It was left as we found it.');
                return;
            }

            $connection->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");

            $databases = $connection->select('SHOW DATABASES');

            $matchingCount = collect($databases)->where('Database', $dbName)->count();

            if ($matchingCount) {
                $this->console->info('Database successfully created');
                return;
            }

            $this->console->error('Could not create database!');
        } catch (Exception $exception) {
            $this->console->error("Error creating database: {$exception->getMessage()}");
        }
    }

    /**
     * Creates the Sqlite Database
     *
     * @return void
     */
    protected function createSqliteDatabase():void
    {
        $this->console->info('Creating sqlite file.');
        $this->shell->inDirectory(config('lambo.store.project_path'), 'touch database/database.sqlite');
    }
}
