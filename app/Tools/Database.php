<?php

namespace App\Tools;

use PDO;

class Database
{
    private $dsn;
    private $username;
    private $password;

    public function fill(string $type, string $host, $port, string $username, $password): self
    {
        $this->dsn = "{$type}:host={$host};port={$port}";
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    public function fillFromUrl(string $url): self
    {
        $url = parse_url($url);

        return $this->fill($url['scheme'], $url['host'], $url['port'], $url['user'], $url['pass']);
    }

    public function fillFromLamboStore(array $store): self
    {
        return $this->fill(
            $type = 'mysql',
            $host = $store['database_host'],
            $port = $store['database_port'],
            $username = $store['database_username'],
            $password = $store['database_password']
        );
    }

    public function ensureExists(string $databaseName = null)
    {
        $dsn = is_null($databaseName)
            ? $this->dsn
            : "{$this->dsn};dbname={$databaseName}";

        new PDO($dsn, $this->username, $this->password);
    }

    public function create(string $databaseName)
    {
        $connection = new PDO($this->dsn, $this->username, $this->password);
        return $connection->exec("CREATE DATABASE IF NOT EXISTS {$databaseName};") === 1;
    }
}
