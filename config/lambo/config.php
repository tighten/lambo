<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Editor
    |--------------------------------------------------------------------------
    |
    | The editor command name to open in the project directory after
    | creating the project. You may set to false not to open any.
    |
    | Option: string | false
    |
    */
    'editor' => false,

    /*
    |--------------------------------------------------------------------------
    | Commit message
    |--------------------------------------------------------------------------
    |
    | The editor command name to open in the project directory after
    | creating the project. You may set to false not to open any.
    |
    | Option: string
    |
    */
    'commit' => 'Initial commit.',

    /*
    |--------------------------------------------------------------------------
    | Path
    |--------------------------------------------------------------------------
    |
    | Where to install the application. Setting it to
    | false will use the current working directory.
    |
    | Options: boolean | string
    | Example: '~/Sites'
    |
    */
    'path' => false,

    /*
    |--------------------------------------------------------------------------
    | Dev Release
    |--------------------------------------------------------------------------
    |
    | Whether to use the develop branch instead of master.
    |
    | Option: boolean
    |
    */
    'dev' => false,

    /*
    |--------------------------------------------------------------------------
    | Auth scaffolding
    |--------------------------------------------------------------------------
    |
    | Whether to run Artisan's auth:make command to
    | scaffold authentication routes and views.
    |
    | Option: boolean
    |
    */
    'auth' => false,

    /*
    |--------------------------------------------------------------------------
    | Node
    |--------------------------------------------------------------------------
    |
    | If set to true, will try 'yarn' if available, or 'npm install'.
    | (If either installed). You may also specify which one as a string.
    |
    | Options: boolean, 'yarn', 'npm'
    |
    */
    'node' => false,

    /*
    |--------------------------------------------------------------------------
    | Browser
    |--------------------------------------------------------------------------
    |
    | If set to true, will run 'valet open' after project installation.
    | You may also specify which browser to run.
    |
    | Options: boolean, string
    | Example: '/Applications/Google Chrome Canary.app'
    |
    */
    'browser' => true,

    /*
    |--------------------------------------------------------------------------
    | Valet Link
    |--------------------------------------------------------------------------
    |
    | Whether create a Valet link to the project directory. Handy
    | when parent folder is not 'Valet parked'
    |
    | Options: boolean
    |
    */
    'link' => false,

    /*
    |--------------------------------------------------------------------------
    | TLD
    |--------------------------------------------------------------------------
    |
    | Project's Top Level Domain
    |
    | Options: string
    |
    */
    'tld' => '.test',

    /*
    |--------------------------------------------------------------------------
    | Create database
    |--------------------------------------------------------------------------
    |
    | If set to true, will create the database for you. Make sure
    | to have the credentials set for the MySQL connection.
    |
    | Options: false, 'mysql', 'sqlite'
    |
    */
    'database' => true,

    /*
    |--------------------------------------------------------------------------
    | Database host connection
    |--------------------------------------------------------------------------
    |
    | The credentials for the database host connection.
    |
    */
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_username' => 'root',
    'db_password' => '',

];
