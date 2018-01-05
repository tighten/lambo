<?php

/*
 * Here goes the illuminate/database component configuration. Once
 * installed, the configuration below is used to configure your
 * database component.
 */
return [
    /*
     * If true, migrations commands will be available.
     */
    'with-migrations' => false,

    /*
     * If true, seeds commands will be available.
     */
    'with-seeds' => false,

    /*
     * Here goes the application database connection configuration. By
     * default, we use `sqlite` as a driver. Feel free to use another
     * driver, be sure to check the database component documentation.
     */
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../database/database.sqlite',
        ],
        'mysql' => [
            'driver'    => 'mysql',
            'database'  => '',
            'username'  => 'root',
            'password'  => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ],
    ],
];
