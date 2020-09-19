<?php

namespace App\Actions;

use App\Shell;
use App\Tools\Database;
use Illuminate\Support\Str;

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
            app('console-writer')->warn("No database connection available using credentials <fg=yellow>mysql://{$user}:****@{$host}:{$port}</>");
            return app('console-writer')->warn($this->getFailMessage());
        }

        $process = $this->shell->execInProject("php artisan migrate{$this->withQuiet()}");

        if (! $process->isSuccessful()) {
            app('console-writer')->warn("Failed to run {$process->getCommandLine()}");
            return app('console-writer')->warn($this->getFailMessage());
        }

        return app('console-writer')->verbose()->success('Database migrated');
    }

    protected function getFailMessage(): string
    {
        $frontend = Str::of(config('lambo.store.frontend'))->title();
        return  "{$frontend} will not run without a migrated database. You will need to run database migrations manually.";
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
