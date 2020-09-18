<?php

namespace App\Actions\Spike;

use App\Actions\AbortsCommands;
use App\Tools\Database;
use App\Tools\Takeout;

class ConfigureDatabase
{
    use AbortsCommands;

    public function __invoke()
    {
        app('console-writer')->logStep('Configuring database');

        // 1. Find/Configure a database
        // 2. Find/Create a schema for the project
        // 3. Migrate the database.

        $host = config('lambo.store.database_host');
        $user = config('lambo.store.database_username');
        $password = config('lambo.store.database_password');
        $port = config('lambo.store.database_port');

        $mysqlConnected = app(Database::class)->find("mysql://{$user}:{$password}@{$host}:{$port}");
        $mysqlConnected
            ? app('console-writer')->verbose()->ok("Connection successful using credentials <fg=yellow>mysql://{$user}:****@{$host}:{$port}</>")
            : app('console-writer')->fail("No connection to mysql using <fg=yellow>mysql://{$user}:****@{$host}:{$port}</>");

        $postgreConnected = app(Database::class)->find("pgsql://{$user}:{$password}@{$host}:{$port}");
        $postgreConnected
            ? app('console-writer')->verbose()->ok("Connection successful using credentials <fg=yellow>pgsql://{$user}:****@{$host}:{$port}</>")
            : app('console-writer')->verbose()->fail("No connection to postgre using credentials <fg=yellow>pgsql://{$user}:****@{$host}:{$port}</>");

        $takeout = app(Takeout::class);
        if (! $takeout->exists()) {
            return;
        }

        app('console-writer')->verbose()->newLine();
        app('console-writer')->verbose()->note('The following takeout database containers are available:');
        $containers = $takeout->only(['mysql', 'postgresql'])->list();
        app('console-writer')->verbose()->table(
            ['Container Id', 'Names', 'Status', 'Ports',],
            $containers
        );

        $choice = app('console')->choice("Would you like to start one and try again?", ['yes', 'no'], 0);
        if ($choice === 'no') {
            return;
        }

        $containerNames = $this->containerNames($containers);

        $choice = app('console')->choice('Choose a container to start', $containerNames, 0);

        $takeout->start($choice)
            ? app('console-writer')->verbose()->success("Started <fg=yellow>{$choice}</>")
            : app('console-writer')->verbose()->warn("Failed to start <fg=yellow>{$choice}</>");

        sleep(2);

        $mysqlConnected = app(Database::class)->find("mysql://{$user}:{$password}@{$host}:{$port}");
        $mysqlConnected
            ? app('console-writer')->verbose()->ok("Successfully connected to MySQL using credentials <fg=yellow>mysql://{$user}:****@{$host}:{$port}</>")
            : app('console-writer')->fail("No connection to MySQL using <fg=yellow>mysql://{$user}:****@{$host}:{$port}</>");

        $postgreConnected = app(Database::class)->find("pgsql://{$user}:{$password}@{$host}:{$port}");
        $postgreConnected
            ? app('console-writer')->verbose()->ok("Successfully connected to PostgreSQL using credentials <fg=yellow>pgsql://{$user}:****@{$host}:{$port}</>")
            : app('console-writer')->verbose()->fail("No connection to PostgreSQL using credentials <fg=yellow>pgsql://{$user}:****@{$host}:{$port}</>");
    }

    protected function containerNames(array $containers): array
    {
        return collect($containers)->pluck('names')->toArray();
    }
}
