<?php

namespace App;

class Paths
{
    public function configDir()
    {
        return config('home_dir') . '/.lambo';
    }

    public function configFile()
    {
        return $this->configDir() . '/config.json';
    }

    public function afterFile()
    {
        return $this->configDir() . '/after.json';
    }
}
