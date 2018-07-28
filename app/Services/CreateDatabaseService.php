<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class CreateDatabaseService
{
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * @var Command
     */
    protected $console;

    public function __construct(Command $console)
    {
        $this->console      = $console;

        $this->connection   = DB::connection('host_database');
    }

    public function handle(): void
    {
        $this->console->alert('Creating database...');

        $appName = $this->console->argument('appName');

        $databases = $this->connection->select('SHOW DATABASES');

        $exists = collect($databases)->filter(function ($item, $key) use ($appName) {
            return $item->Database === $appName;
        })->count();

        if ($exists) {
            $this->console->error('Database existed already!');
            return;
        }

        $this->connection->statement("CREATE DATABASE IF NOT EXISTS {$appName}");

        $databases = $this->connection->select('SHOW DATABASES');

        $checkExists = collect($databases)->filter(function ($item, $key) use ($appName) {
            return $item->Database === $appName;
        })->count();

        if ($checkExists) {
            $this->console->info('Database successfully created');
            return;
        }

        $this->console->error('Could not create database!');
    }
}
