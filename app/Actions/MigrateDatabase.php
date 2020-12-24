<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;
use App\Tools\Database;
use PDOException;

class MigrateDatabase
{
    use AbortsCommands;

    protected $shell;
    protected $database;
    protected $consoleWriter;

    public function __construct(Shell $shell, Database $database, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->database = $database;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        if (! config('lambo.store.migrate_database')) {
            return;
        }

        $this->consoleWriter->logStep('Running database migrations');

        try {
            $this->database
                ->fillFromLamboStore(config('lambo.store'))
                ->ensureExists(config('lambo.store.database_name'));

            $process = $this->shell->execInProject("php artisan migrate{$this->withQuiet()}");

            return $process->isSuccessful()
                ? $this->consoleWriter->success('Database migrated')
                : $this->consoleWriter->warn("Failed to run {$process->getCommandLine()}");
        } catch (PDOException $e) {
            $this->consoleWriter->warn($e->getMessage());
            return $this->consoleWriter->warn($this->failureMigrateError());
        }
    }

    protected function failureMigrateError(): string
    {
        return sprintf(
            "Skipping database migration using credentials <fg=yellow>mysql://%s:****@%s:%s</>\nYou will need to run the database migrations manually.",
            config('lambo.store.database_username'),
            config('lambo.store.database_host'),
            config('lambo.store.database_port')
        );
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
