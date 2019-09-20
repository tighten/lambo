<?php

namespace App\ActionsOnInstall;

use App\Support\BaseAction;
use DB;
use Exception;

class CreateDatabase extends BaseAction
{
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
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => config('lambo.config.db_host'),
            'database.connections.mysql.port' => config('lambo.config.db_port'),
            'database.connections.mysql.username' => config('lambo.config.db_username'),
            'database.connections.mysql.password' => config('lambo.config.db_password'),
        ]);
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

            $dbName = config('lambo.store.db_name');

            $databases = DB::select('SHOW DATABASES');

            $matchingCount = collect($databases)->where('Database', $dbName)->count();

            if ($matchingCount > 0) {
                $this->console->alert('Database already existed! It was left as we found it.');
                return;
            }

            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");

            $databases = DB::select('SHOW DATABASES');

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
