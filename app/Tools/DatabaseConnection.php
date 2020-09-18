<?php

namespace App\Tools;

use Exception;
use PDO;

class DatabaseConnection
{
    public function testConnection(string $databaseType, string $host, string $username, string $password, int $port, string $schema = null): bool
    {
        $dsn = "{$databaseType}:host={$host};port={$port};" . is_null($schema) ? '' : ";dbname={$schema}";
        try {
            new PDO($dsn, $username, $password);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createSchema(string $databaseType, string $host, string $username, string $password, int $port, string $schema): bool
    {
        try {
            $connection = new PDO("{$databaseType}:host={$host};port={$port};", $username, $password);
            $connection->exec("CREATE DATABASE {$schema};");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
