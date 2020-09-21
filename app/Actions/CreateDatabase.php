<?php

namespace App\Actions;

use App\Shell;
use App\Tools\Database;

class CreateDatabase
{
    use AbortsCommands;

    protected $finder;
    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
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

        $mysqlAvailable = app(Database::class)
            ->url("mysql://{$user}:{$password}@{$host}:{$port}")
            ->find();

        if (! $mysqlAvailable) {
            app('console-writer')->warn('Skipping database creation.');
            return app('console-writer')->warn("No database connection available using credentials <fg=yellow>mysql://{$user}:****@{$host}:{$port}</>");
        }

        return app(Database::class)
            ->url("mysql://{$user}:{$password}@{$host}:{$port}")
            ->createSchema($schema)
            ? app('console-writer')->verbose()->success('Created a new database ' . $schema)
            : app('console-writer')->verbose()->warn("Failed to create database {$schema} You will need to configure your database manually.");
    }
}
