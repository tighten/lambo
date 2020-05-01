<?php

namespace App;

trait GeneratesHelpScreens
{
    protected $indent = 30;

    public function makeSpaces($count)
    {
        return str_repeat(" ", $this->indent - $count);
    }

    public function renderOptions()
    {
        $this->line("\n<comment>Options:</comment>");
        $this->line("  <info>Todo</info> Options coming soon");
        /* @todo
        foreach ($options as $option) {
           return "  <info>{$flag}</info>{$spaces}{$description}";
        }
        */
    }

    public function genericOptions()
    {
        /*
          -h, --help             Display this help message
          -q, --quiet            Do not output any message
          -V, --version          Display this application version
              --ansi             Force ANSI output
              --no-ansi          Disable ANSI output
          -n, --no-interaction   Do not ask any interactive question
              --env[=ENV]        The environment the command should run under
          -v|vv|vvv, --verbose   Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
          */
    }
}
