<?php

namespace App\Tools;

class Database
{
    private $databaseType;
    private $pass;
    private $host;
    private $port;
    private $user;

    public function url(string $url)
    {
        list($this->databaseType, $this->host, $this->port, $this->user, $this->pass) = array_values(parse_url($url));
        return $this;
    }

    public function find(string $schema = null): bool
    {
        return is_null($schema)
            ?  (app(DatabaseConnection::class))->testConnection($this->databaseType, $this->host, $this->user, $this->pass, $this->port)
            :  (app(DatabaseConnection::class))->testConnection($this->databaseType, $this->host, $this->user, $this->pass, $this->port, $schema);
    }

    public function createSchema(string $databaseName): bool
    {
        return (app(DatabaseConnection::class))->createSchema($this->databaseType, $this->host, $this->user, $this->pass, $this->port, $databaseName);
    }
}
