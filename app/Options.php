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
            'short' => 'f',
            'long' => 'frontend',
            'param_description' => '"FRONTEND"',
            'cli_description' => "Specify the <info>FRONTEND</info> framework to use. Must be one of bootstrap, react or vue",
        ],
        [
            'long' => 'dbname',
            'param_description' => 'DBNAME',
            'cli_description' => "Specify the database name",
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
            'long' => 'create-db',
            'cli_description' => "Create a new MySQL database",
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
            'cli_description' => "Use quiet mode to hide most messages from lambo",
        ],
        [
            'short' => 't',
            'long' => 'quiet-shell',
            'cli_description' => "Use quiet-shell mode to hide output from shell based commands (git and npm etc.) Use with -q, --quiet to silence everything",
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
            // 'short' => 'n',
            'long' => 'node',
            'cli_description' => "Run <info>'npm install'</info> after creating the project",
        ],
        [
            'short' => 'x',
            'long' => 'mix',
            'cli_description' => "Run <info>'npm run dev'</info> after creating the project to compile assets",
        ],
        [
            'long' => 'full',
            'cli_description' => "Shortcut of --create-db --link --secure --auth --node --mix",
        ],
    ];

    public function all()
    {
        return $this->options;
    }
}
