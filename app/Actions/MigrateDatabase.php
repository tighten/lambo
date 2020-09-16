<?php

namespace App\Actions;

use App\Shell;

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
        $process = $this->shell->execInProject("php artisan migrate{$this->withQuiet()}");
        $this->abortIf(! $process->isSuccessful(), 'Failed to run database migrations successfully', $process);
        app('console-writer')->verbose()->success('Database migrated');
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
