<?php

namespace App\Actions;

use App\Shell\Shell;
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

        if (! $this->mysqlExists()) {
            return "MySQL does not seem to be installed. Skipping new database creation.";
        }

        $this->logStep('Creating database');

        $process = $this->shell->execInProject($this->command());

        $this->abortIf(! $process->isSuccessful(), "The new database was not created.", $process);

        return 'Created a new database ' . config('lambo.store.database_name');
    }

    protected function mysqlExists()
    {
        return $this->finder->find('mysql') !== null;
    }

    protected function command()
    {
        return sprintf(
            'mysql --user=%s --password=%s -e "CREATE DATABASE IF NOT EXISTS %s";',
            config('lambo.store.database_username'),
            config('lambo.store.database_password'),
            config('lambo.store.database_name')
        );
    }
}
