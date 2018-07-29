<?php

namespace App\Actions;

use App\Support\BaseAction;
use App\Services\CreateMySQLDatabaseService;

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
            app()->make(CreateMySQLDatabaseService::class, ['console' => $this->console])->handle();
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
