<?php

namespace App\Tools;

use Exception;
use Illuminate\Support\Str;
use PDO;

class Database
{
    private $dsn;
    private $username;
    private $password;

    public function url(string $url)
    {
        $url = parse_url($url);
        $databaseType = $url['scheme'];
        $host = $url['host'];
        $port = $url['port'];
        $this->username = $url['user'];
        $this->password = $url['pass'];

        $this->dsn = "{$databaseType}:host={$host};port={$port}";
        return $this;
    }

    public function find(string $schema = null): bool
    {
        $dsn = is_null($schema)
            ? $this->dsn
            : "{$this->dsn};dbname={$schema}";

        try {
            new PDO($dsn, $this->username, $this->password);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createSchema(string $databaseName): bool
    {
        try {
            $connection = new PDO($this->dsn, $this->username, $this->password);
            $connection->exec("CREATE DATABASE {$databaseName};");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
