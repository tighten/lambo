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
    | Option: string
    |
    */
    'editor' => 'pstorm',

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
    'message' => 'Initial commit.',

    /*
    |--------------------------------------------------------------------------
    | Path
    |--------------------------------------------------------------------------
    |
    | Specify where to install the application. Setting it
    | to false will use the current working directory.
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
    | Here yoy may choose the develop branch instead of master,
    | getting the beta install.
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
    | Whether to use Artisan to scaffold all of the
    | routes and views you need for authentication.
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
    'browser' => false,

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
    | Create database
    |--------------------------------------------------------------------------
    |
    | If set to true, will create the database for you. Make sure
    | to have the credentials set for the MySQL connection.
    |
    | Options: false, 'mysql', 'sqlite'
    |
    */
    'database' => false,

    /*
    |--------------------------------------------------------------------------
    | Database host connection
    |--------------------------------------------------------------------------
    |
    | The credentials for the database host connection.
    |
    */
    'db_host'      => '127.0.0.1',
    'db_port'      => '3306',
    'db_username'  => 'root',
    'db_password'  => '',

];