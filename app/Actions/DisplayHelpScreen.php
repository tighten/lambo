<?php

namespace App\Actions;

class DisplayHelpScreen
{
    protected $indent = 30;

    protected $helpText = "
<comment>Usage:</comment>
  lambo new myApplication [arguments]

<comment>Commands (lambo COMMANDNAME):</comment>
   <info>help-screen</info>                  Display this screen

   <info>make-config</info>                  Generate config file
   <info>edit-config</info>                  Edit config file

   <info>make-after</info>                   Generate after file
   <info>edit-after</info>                   Edit after file

<comment>Options (lambo new myApplication OPTIONS):</comment>";

    protected $options = [
        "-e, --editor EDITOR" => "Specify an editor to run <info>'EDITOR .'</info> with after",
        "-m, --message \"message\"" => "Customize the initial commit message",
        "-p, --path PATH" => "Customize the path in which the new project will be created",
        "-d, --dev" => "Install Laravel using the develop branch",
        "-a, --auth" => "Scaffold the routes and views for basic Laravel auth",
        "-n, --node" => "Run <info>'npm install'</info> after creating the project",
        "-b, --browser \"browser path\"" => "Open the site in the specified browser",
        "-l, --link" => "Create a Valet link to the project directory",
        "-s, --secure" => "Generate and use https with Valet",
        "-q, --quiet" => "Use muffler (quiet mode)",
        "--create-db" => "Create a new MySQL database",
        "--dbuser" => "Specify the database user",
        "--dbpassword" => "Specify the database password",
        "--vue" => "Specify Vue as the frontend",
        "--bootstrap" => "Specify Bootstrap as the frontend",
        "--react" => "Specify React as the frontend",
    ];

    public function __invoke()
    {
        foreach (explode("\n", $this->helpText) as $line) {
            app('console')->line($line);
        }

        foreach ($this->options as $option => $description) {
            $spaces = $this->makeSpaces(strlen($option));
            app('console')->line("  <info>{$option}</info>{$spaces}{$description}");
        }
    }

    public function makeSpaces($count)
    {
        return collect(range(1, $this->indent - $count))->map(function () {
            return ' ';
        })->implode('');
    }
}
