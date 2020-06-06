<?php

namespace App\Actions;

class DisplayLamboWelcome
{
    protected $lamboLogo = "
     __                    __               :version:
    / /   ____ _____ ___  / /_  ____
   / /   / __ `/ __ `__ \/ __ \/ __ \
  / /___/ /_/ / / / / / / /_/ / /_/ /
 /_____/\__,_/_/ /_/ /_/_.___/\____/";
    public function __construct()
    {
        $this->lamboLogo = str_replace(':version:', config('app.version'), $this->lamboLogo);
    }

    public function __invoke()
    {
        // Extra space on the end fixes an issue with console when it ends with backslash
        $logo = collect(explode("\n", $this->lamboLogo))->reduce(function ($carry, $line) {
            return sprintf("%s%s\n", $carry, $line);
        });
        app('console-writer')->ignoreVerbosity()->text("<info>${logo}</info>");
        app('console-writer')->ignoreVerbosity()->text("<info>Lambo:</info> Super-powered <comment>'laravel new'</comment> for Laravel and Valet.");
        app('console-writer')->newLine();
    }
}
