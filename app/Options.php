<?php

namespace App;

class Options
{
    protected $options = [
        /** Parameters first, then flags */
        [
            'short' => 'e',
            'long' => 'editor',
            'param_description' => 'EDITOR',
            'cli_description' => "Specify an editor to run <info>'EDITOR .'</info> with after",
        ],
        [
            'short' => 'm',
            'long' => 'message',
            'param_description' => '"message"',
            'cli_description' => "Customize the initial commit message (wrap with quotes!)",
        ],
        [
            'short' => 'p',
            'long' => 'path',
            'param_description' => 'PATH',
            'cli_description' => "Customize the path in which the new project will be created",
        ],
        [
            'short' => 'b',
            'long' => 'browser',
            'param_description' => '"browser path"',
            'cli_description' => "Open the site in the specified browser (macOS-only)",
        ],
        [
            'long' => 'create-db',
            'param_description' => 'DBNAME', // Maybe?? @todo
            'cli_description' => "Create a new MySQL database",
        ],
        [
            'long' => 'dbuser',
            'param_description' => 'USERNAME',
            'cli_description' => "Specify the database user",
        ],
        [
            'long' => 'dbpassword',
            'param_description' => 'PASSWORD',
            'cli_description' => "Specify the database password",
        ],
        [
            'short' => 'l',
            'long' => 'link',
            'cli_description' => "Create a Valet link to the project directory",
        ],
        [
            'short' => 's',
            'long' => 'secure',
            'cli_description' => "Generate and use an HTTPS cert with Valet",
        ],
        [
            'short' => 'q',
            'long' => 'quiet',
            'cli_description' => "Use quiet mode to hide most messages",
        ],
        [
            'short' => 'd',
            'long' => 'dev',
            'cli_description' => "Install Laravel using the develop branch",
        ],
        [
            'short' => 'a',
            'long' => 'auth',
            'cli_description' => "Scaffold the routes and views for basic Laravel auth",
        ],
        [
            'short' => 'N',
            'long' => 'node',
            'cli_description' => "Run <info>'npm install'</info> after creating the project",
        ],
        [
            'long' => 'vue',
            'cli_description' => "Specify Vue as the frontend",
        ],
        [
            'long' => 'react',
            'cli_description' => "Specify React as the frontend",
        ],
        [
            'long' => 'bootstrap',
            'cli_description' => "Specify Bootstrap as the frontend",
        ],
    ];

    public function all()
    {
        return $this->options;
    }
}
