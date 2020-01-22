<?php

namespace App\Actions;

use App\Shell\Shell;
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
        if (! (config('lambo.store.create_database') || config('lambo.store.full'))) {
            return;
        }

        $this->logStep('Creating database');

        if ($this->mysqlExists()) {
            $process = $this->shell->execInProject($this->getCommand());

            if ($process->isSuccessful()) {
                $this->info($this->getFeedback());
                return;
            }

            $this->abort("The new database was not created.", $process);

        }

        $this->warn("MySql does not seem to be installed. Skipping new database creation.");
    }

    protected function mysqlExists()
    {
        return $this->finder->find('mysql') !== null;
    }

    protected function getCommand()
    {
        return sprintf(
            'mysql --user=%s --password=%s -e "CREATE DATABASE IF NOT EXISTS %s";',
            config('lambo.store.database_username'),
            config('lambo.store.database_password'),
            config('lambo.store.database_name')
        );
    }

    protected function getFeedback()
    {
        return sprintf(
            "Created a new database '%s'",
            config('lambo.store.database_name'));
    }

}
