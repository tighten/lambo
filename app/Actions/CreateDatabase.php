<?php

namespace App\Actions;

use App\Shell;
use Illuminate\Support\Str;
use Symfony\Component\Process\ExecutableFinder;

class CreateDatabase
{
    use AbortsCommands;

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

        app('console-writer')->logStep('Creating database');

        if (! $this->mysqlExists() || ! $this->mysqlServerRunning()) {
            app('console-writer')->warn('Skipping database creation');
            app('console-writer')->warn("Either MySQL is not installed or it's not running.");
            return;
        }

        $process = $this->shell->exec($this->command());

        $this->abortIf(! $process->isSuccessful(), "The new database was not created.", $process);

        app('console-writer')->success('Created a new database ' . config('lambo.store.database_name'));
    }

    protected function mysqlExists()
    {
        return ! is_null($this->finder->find('mysql'));
    }

    private function mysqlServerRunning(): bool
    {
        app('console-writer')->text('Searching for a running MySQL server');
        $output = $this->shell->exec('mysql.server status')->getOutput();
        return Str::of($output)->contains('MySQL running');
    }

    private function command()
    {
        return sprintf(
            'mysql --user=%s --password=%s -e "CREATE DATABASE IF NOT EXISTS %s";',
            config('lambo.store.database_username'),
            config('lambo.store.database_password'),
            config('lambo.store.database_name')
        );
    }
}
