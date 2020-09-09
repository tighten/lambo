<?php

namespace App\Configuration;

use Illuminate\Support\Str;

abstract class LamboConfiguration
{
    const EDITOR = 'editor';
    const PROJECT_NAME = 'project_name';
    const ROOT_PATH = 'root_path';
    const WITH_OUTPUT = 'with_output';
    const USE_DEVELOP_BRANCH = 'dev';
    const CREATE_DATABASE = 'create_database';
    const DATABASE_NAME = 'database_name';
    const DATABASE_USERNAME = 'database_username';
    const DATABASE_PASSWORD = 'database_password';
    const FRONTEND_FRAMEWORK = 'frontend';
    const FULL = 'full';
    const TLD = 'tld';
    const COMMIT_MESSAGE = 'commit_message';
    const VALET_LINK = 'valet_link';
    const VALET_SECURE = 'valet_secure';
    const BROWSER = 'browser';
    const WITH_TEAMS = 'with_teams';

    public function __construct(array $keyMap)
    {
        $settings = $this->getSettings();

        collect($keyMap)->each(function ($item, $key) use ($settings) {
            $this->$item = $this->get($key, $settings);
        });
    }

    abstract protected function getSettings(): array;

    protected function get(string $key, array $array)
    {
        if (array_key_exists($key, $array)) {

            if ($array[$key] === '') {
                return null;
            }

            if (in_array(Str::lower($array[$key]), ["1", "true", "on", "yes"])) {
                return true;
            }

            if (in_array(Str::lower($array[$key]), ["0", "false", "off", "no"])) {
                return false;
            }

            return $array[$key];
        }

        return null;
    }

    public function __get($name)
    {
        return null;
    }
}
