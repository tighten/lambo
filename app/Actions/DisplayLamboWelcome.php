<?php

namespace App\Actions;

class DisplayLamboWelcome
{
    protected $lamboLogo = "
     __                     _
    / /    __ _  _ __ ___  | |__    ___
   / /    / _` || '_ ` _ \ | '_ \  / _ \
  / /___ | (_| || | | | | || |_) || (_) |
  \____/  \__,_||_| |_| |_||_.__/  \___/
";

    protected $welcomeText = "
<info>Lambo:</info> Super-powered <comment>'laravel new'</comment> for Laravel and Valet.
Version :version:";

    public function __construct()
    {
        $this->welcomeText = str_replace(':version:', config('app.version'), $this->welcomeText);
    }

    public function __invoke()
    {
        foreach (explode("\n", $this->lamboLogo) as $line) {
            // Extra space on the end fixes an issue with console when it ends with backslash
            app('console')->info($line . " ");
        }

        foreach (explode("\n", $this->welcomeText) as $line) {
            // Extra space on the end fixes an issue with console when it ends with backslash
            app('console')->info($line . " ");
        }
    }
}
