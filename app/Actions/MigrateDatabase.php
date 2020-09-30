<?php

namespace App\Actions;

use App\Shell;
use App\Tools\Database;
use PDOException;

class MigrateDatabase
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;
    protected $database;

    public function __construct(Shell $shell, Database $database)
    {
        $this->shell = $shell;
        $this->database = $database;
    }

    public function __invoke()
    {
        if (! config('lambo.store.migrate_database')) {
            return;
        }

        app('console-writer')->logStep('Running database migrations');

        $host = config('lambo.store.database_host');
        $user = config('lambo.store.database_username');
        $password = config('lambo.store.database_password');
        $port = config('lambo.store.database_port');
        $schema = config('lambo.store.database_name');

        try {
            $this->database
                ->url("mysql://{$user}:{$password}@{$host}:{$port}")
                ->exists($schema);

            $process = $this->shell->execInProject("php artisan migrate{$this->withQuiet()}");
            return $process->isSuccessful()
                ? app('console-writer')->success('Database migrated')
                : app('console-writer')->warn("Failed to run {$process->getCommandLine()}");
        } catch (PDOException $e) {
            app('console-writer')->warn("Skipping database migration using credentials <fg=yellow>mysql://{$user}:****@{$host}:{$port}</>");
            app('console-writer')->warn($e->getMessage());
            return app('console-writer')->warn('You will need to run the database migrations manually.');
        }
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
