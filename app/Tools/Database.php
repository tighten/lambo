<?php

namespace App\Tools;

use PDO;

class Database
{
    private $databaseType;
    private $host;
    private $port;
    private $username;
    private $password;

    public function url(string $url)
    {
        list($this->databaseType, $this->host, $this->port, $this->username, $this->password) = array_values(parse_url($url));
        return $this;
    }

    public function find(string $schema = null): bool
    {
        $dsn = "{$this->databaseType}:host={$this->host};port={$this->port};" . is_null($schema) ? '' : ";dbname={$schema}";
        new PDO($dsn, $this->username, $this->password);
        return true;
    }

    public function createSchema(string $databaseName): bool
    {
        $connection = new PDO("{$this->databaseType}:host={$this->host};port={$this->port};", $this->username, $this->password);
        $connection->exec("CREATE DATABASE {$databaseName};");
        return true;
    }
}
