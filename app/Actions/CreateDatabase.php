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
        if (! (config('lambo.store.create_database') || config('lambo.store.full'))) {
            return;
        }

        if ($this->mysqlExists()) {
            $createDatabaseCommand = sprintf(
                'mysql --user=%s --password=%s -e "CREATE DATABASE IF NOT EXISTS %s";',
                config('lambo.store.database_username'),
                config('lambo.store.database_password'),
                config('lambo.store.database_name')
            );
            $this->shell->execInProject($createDatabaseCommand);
            app('console')->info(sprintf(
                    '[ mysql ] created new database "%s"',
                    config('lambo.store.database_name'))
            );
            return;
        }

        app('console')->warn("MySql does not seem to be installed. Skipping new database creation.");
    }

    protected function mysqlExists()
    {
        return $this->finder->find('mysql') !== null;
    }
}
