<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class MigrateDatabase
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $process = $this->shell->execInProject("php artisan migrate{$this->withQuiet()}");
        $this->abortIf(! $process->isSuccessful(), 'Failed to run database migrations successfully', $process);
        $this->consoleWriter->verbose()->success('Database migrated');
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
