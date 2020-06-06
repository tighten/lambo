<?php

namespace App\Actions;

use App\Shell;
use Illuminate\Support\Str;
use Symfony\Component\Process\ExecutableFinder;

class CreateDatabase
{
    use LamboAction;

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

        $this->abortIf(! $this->mysqlExists(), "I can't create a database for your project because MySQL does not seem to be installed.");

        if (! $this->mysqlServerRunning()) {
            app('console-writer')->ignoreVerbosity()->warn('Skipping database creation');
            app('console-writer')->ignoreVerbosity()->warn("I can't create a database for your project because your MySQL server does not seem to be running.");
            return;
        }

        $process = $this->shell->exec($this->command());

        $this->abortIf(! $process->isSuccessful(), "The new database was not created.", $process);

        app('console-writer')->success('Created a new database ' . config('lambo.store.database_name'));
    }

    protected function mysqlExists()
    {
        return $this->finder->find('mysql') !== null;
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
