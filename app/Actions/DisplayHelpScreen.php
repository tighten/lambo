<?php

namespace App\Actions;

class DisplayHelpScreen
{
    protected $indent = 30;

    protected $helpText = "
<comment>Usage:</comment>
  lambo new myApplication [arguments]

<comment>Commands (lambo COMMANDNAME):</comment>
   <info>make-config</info>                  Generate config file
   <info>edit-config</info>                  Edit config file

   <info>make-after</info>                   Generate after file
   <info>edit-after</info>                   Edit after file

<comment>Options (lambo new myApplication OPTIONS):</comment>";

    protected $options = [
        "-h, --help" => "Show brief help",
        "-e, --editor EDITOR" => "Specify an editor to run <info>'EDITOR .'</info> with after",
        "-m, --message \"message\"" => "Customize the initial commit message",
        "-p, --path PATH" => "Customize the path in which the new project will be created",
        "-d, --dev" => "Use Composer to install on the develop branch",
        "-a, --auth" => "Use Artisan to scaffold all of the routes and views you need for authentication",
        "-n, --node" => "Runs <info>'npm install'</info> after creating the project",
        "-b, --browser \"browser path\"" => "Opens site in specified browser",
        "-l, --link" => "Creates a Valet link to the project directory",
        "-s, --secure" => "Generate and use https with Valet",
        "-q, --quiet" => "Use muffler (quiet mode)",
    ];

    protected $flags = [
        "--create-db" => "Create a new MySql database",
        "--dbuser" => "Specify the database user",
        "--dbpassword" => "Specify the database password",
        "--vue" => "Specify Vue as the frontend",
        "--bootstrap" => "Specify Bootstrap as the frontend",
        "--react" => "Specify React as the frontend",
    ];

    public function __construct()
    {
        $this->helpText = str_replace(':version:', config('app.version'), $this->helpText);
    }

    public function __invoke()
    {
        foreach (explode("\n", $this->helpText) as $line) {
            // Extra space on the end fixes an issue with console when it ends with backslash
            app('console')->line($line . " ");
        }

        // @todo line up
        foreach ($this->options as $option => $description) {
            $spaces = $this->makeSpaces(strlen($option));
            app('console')->line("  <info>{$option}</info>{$spaces}{$description}");
        }

        // @todo line up
        foreach ($this->flags as $flag => $description) {
            $spaces = $this->makeSpaces(strlen($flag));
            app('console')->line("  <info>{$flag}</info>{$spaces}{$description}");
        }
    }

    public function makeSpaces($count)
    {
        // @todo there must be a more elegant way
        $string = '';

        for ($i = 1; $i <= $this->indent - $count; $i++) {
            $string .= ' ';
        }

        return $string;
    }
}
