<?php

namespace App\Actions;

use App\Shell;
use App\Tools\Database;

class MigrateDatabase
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        app('console-writer')->logStep('Running database migrations');

        $host = config('lambo.store.database_host');
        $user = config('lambo.store.database_username');
        $password = config('lambo.store.database_password');
        $port = config('lambo.store.database_port');

        $mysqlAvailable = app(Database::class)
            ->url("mysql://{$user}:{$password}@{$host}:{$port}")
            ->find();

        if (! $mysqlAvailable) {
            app('console-writer')->warn('Skipping database migration.');
            return app('console-writer')->warn("No database connection available using credentials <fg=yellow>mysql://{$user}:****@{$host}:{$port}</>");
        }

        $process = $this->shell->execInProject("php artisan migrate{$this->withQuiet()}");

        if (! $process->isSuccessful()) {
            app('console-writer')->warn("Failed to run {$process->getCommandLine()}");
            $process->getErrorOutput();
            return app('console-writer')->warn($process->getErrorOutput());
        }

        return app('console-writer')->verbose()->success('Database migrated');
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
