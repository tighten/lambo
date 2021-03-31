<?php

namespace App;

class Options
{
    protected $options = [
        /** Parameters first, then flags */
        [
            'short' => 'p',
            'long' => 'path',
            'param_description' => 'PATH',
            'cli_description' => 'Customize the path in which the new project will be created',
        ],
        [
            'short' => 'm',
            'long' => 'message',
            'param_description' => 'MESSAGE',
            'cli_description' => 'Customize the initial commit message (wrap with quotes!)',
        ],
        [
            'short' => 'B',
            'long' => 'branch',
            'param_description' => 'BRANCH',
            'cli_description' => 'The branch that should be created for a new repository <comment>[default: "main"]</comment>',
        ],
        [
            'short' => 'g',
            'long' => 'github',
            'cli_description' => 'Create a new repository on GitHub',
        ],
        [
            'short' => 'b',
            'long' => 'browser',
            'param_description' => 'BROWSER',
            'cli_description' => 'Open the site in the specified <info>BROWSER</info>. E.g. <info>firefox</info>',
        ],
        [
            'long' => 'dbhost',
            'param_description' => 'HOST',
            'cli_description' => 'Specify the database <info>HOST</info>',
        ],
        [
            'long' => 'dbport',
            'param_description' => 'PORT',
            'cli_description' => 'Specify the database <info>PORT</info>',
        ],
        [
            'long' => 'dbname',
            'param_description' => 'NAME',
            'cli_description' => 'Specify the database <info>NAME</info>',
        ],
        [
            'long' => 'dbuser',
            'param_description' => 'USERNAME',
            'cli_description' => 'Specify the database <info>USERNAME</info>',
        ],
        [
            'long' => 'dbpassword',
            'param_description' => 'PASSWORD',
            'cli_description' => 'Specify the database <info>PASSWORD</info>',
        ],
        [
            'long' => 'create-db',
            'cli_description' => 'Create a new MySQL database',
        ],
        [
            'short' => 'f',
            'long' => 'force',
            'cli_description' => 'Forces install even if the directory already exists',
        ],
        [
            'long' => 'migrate-db',
            'cli_description' => 'Run database migrations',
        ],
        [
            'long' => 'inertia',
            'cli_description' => 'Use inertia frontend scaffolding',
        ],
        [
            'long' => 'livewire',
            'cli_description' => 'Use livewire frontend scaffolding',
        ],
        [
            'long' => 'teams',
            'cli_description' => 'Use team features with inertia or livewire',
        ],
        [
            'short' => 'l',
            'long' => 'link',
            'cli_description' => 'Create a Valet link to the project directory',
        ],
        [
            'short' => 's',
            'long' => 'secure',
            'cli_description' => 'Generate and use an HTTPS cert with Valet',
        ],
        [
            'short' => 'd',
            'long' => 'dev',
            'cli_description' => 'Install Laravel using the develop branch',
        ],
        [
            'long' => 'full',
            'cli_description' => 'Shortcut of --create-db --migrate-db --link --secure',
        ],
    ];

    protected $commonOptions = [
        [
            'short' => 'e',
            'long' => 'editor',
            'param_description' => 'EDITOR',
            'cli_description' => "Specify an <info>EDITOR</info> to use",
        ],
        [
            'short' => 'q',
            'long' => 'quiet',
            'cli_description' => 'Do not output to the console (except for user input)',
        ],
        [
            'short' => 'V',
            'long' => 'version',
            'param_description' => 'EDITOR',
            'cli_description' => 'Display Lambo\'s version',
        ],
        [
            'long' => 'ansi',
            'cli_description' => 'Force ANSI output',
        ],
        [
            'long' => 'no-ansi',
            'cli_description' => 'Disable ANSI output',
        ],
        [
            'short' => 'v|vv|vvv',
            'long' => 'verbose',
            'param_description' => 'LEVEL',
            'cli_description' => "Increase the verbosity of messages where <info>LEVEL</info> is 1 for normal output, 2 for more verbose output and 3 for debug",
        ],
    ];

    public function common(): array
    {
        return $this->commonOptions;
    }

    public function all(): array
    {
        return $this->options;
    }
}
