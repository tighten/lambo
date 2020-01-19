<?php

namespace App\Actions;

use App\Shell;
use Symfony\Component\Process\ExecutableFinder;

class CreateDatabase
{
    protected $finder;
    protected $shell;

    public function __construct(Shell $shell, ExecutableFinder $finder)
    {
        $this->finder = $finder;
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (! config('lambo.store.create_database')) {
            return;
        }

        if ($this->mysqlExists()) {
            $createDatabaseCommand = sprintf(
                'mysql --user=%s --password=%s -e "CREATE DATABASE IF NOT EXISTS %s";',
                config('lambo.store.database_username'),
                config('lambo.store.database_password'),
                config('lambo.store.create_database')
            );
            app('console')->info(sprintf(
                'Creating new database "%s"',
                config('lambo.store.create_database'))
            );
            $this->shell->execInProject($createDatabaseCommand);
            return;
        }

        app('console')->warn("MySql doesn't seem to be installed. Skipping new database creation.");
    }

    protected function mysqlExists()
    {
        return $this->finder->find('mysql') !== null;
    }
}
