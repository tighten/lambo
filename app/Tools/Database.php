<?php

namespace App\Tools;

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

    public function exists(string $databaseName = null)
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
