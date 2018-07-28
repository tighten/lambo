<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class CreateMySQLDatabaseService
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

        $dbName = config('lambo-store.db_name');

        $databases = $this->connection->select('SHOW DATABASES');

        $exists = collect($databases)->filter(function ($item, $key) use ($dbName) {
            return $item->Database === $dbName;
        })->count();

        if ($exists) {
            $this->console->error('Database already existed! It was left as we found it.');
            return;
        }

        $this->connection->statement("CREATE DATABASE IF NOT EXISTS {$dbName}");

        $databases = $this->connection->select('SHOW DATABASES');

        $checkExists = collect($databases)->filter(function ($item, $key) use ($dbName) {
            return $item->Database === $dbName;
        })->count();

        if ($checkExists) {
            $this->console->info('Database successfully created');
            return;
        }

        $this->console->error('Could not create database!');
    }
}
