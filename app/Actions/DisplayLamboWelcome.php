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

    public function __invoke()
    {
        foreach (explode("\n", $this->lamboLogo) as $line) {
            // Extra space on the end fixes an issue with console when it ends with backslash
            app('console')->info($line . " ");
        }

        app('console')->alert("Super-powered 'laravel new' with Laravel and Valet.");
    }
}
