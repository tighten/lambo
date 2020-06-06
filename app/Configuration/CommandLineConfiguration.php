<?php

namespace App\Configuration;

class CommandLineConfiguration extends LamboConfiguration
{
    protected function getSettings(): array
    {
        $commandLineConfiguration = app('console')->options();

        foreach (app('console')->arguments() as $key => $value) {
            $commandLineConfiguration[$key] = $value;
        }

        return $commandLineConfiguration;
    }
}
