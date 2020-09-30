<?php

namespace App\Actions;

use App\Shell;
use App\Tools\Database;
use PDOException;

class CreateDatabase
{
    use AbortsCommands;

    protected $finder;
    protected $shell;
    protected $consoleWriter;
    protected $databaseUtil;

    public function __construct(Shell $shell, Database $database)
    {
        $this->shell = $shell;
        $this->databaseUtil = $database;
    }

    public function __invoke()
    {
        if (! config('lambo.store.create_database')) {
            return;
        }

        app('console-writer')->logStep('Creating database');

        $host = config('lambo.store.database_host');
        $user = config('lambo.store.database_username');
        $password = config('lambo.store.database_password');
        $port = config('lambo.store.database_port');
        $schema = config('lambo.store.database_name');

        try {
            $databaseCreated = $this->databaseUtil
                ->url("mysql://{$user}:{$password}@{$host}:{$port}")
                ->create($schema);

            if (! $databaseCreated) {
                return app('console-writer')->warn("Failed to create database '{$schema}' using credentials <fg=yellow>mysql://{$user}:****@{$host}:{$port}</>");
            }
        } catch (PDOException $e) {
            app('console-writer')->warn("Failed to create database '{$schema}' using credentials <fg=yellow>mysql://{$user}:****@{$host}:{$port}</>");
            app('console-writer')->warn($e->getMessage());
            return app('console-writer')->warn('You will need to create the database manually.');
        }

        return app('console-writer')->success("Created a new database '{$schema}'");
    }
}
