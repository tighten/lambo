<?php


namespace App;


trait LogsToConsole
{
    public function alert(string $message)
    {
        app('console')->alert($message);
    }

    public function warn(string $message)
    {
        app('console')->warn($message);
    }

    public function error(string $message)
    {
        app('console')->error($message);
    }

    protected function line(string $message)
    {
        app('console')->line($message);
    }

    protected function info(string $message)
    {
        app('console')->info($message);
    }
}
