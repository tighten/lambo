<?php

namespace App\Services;

use LaravelZero\Framework\Commands\Command;

class DisplayService
{
    protected $console;

    protected $lamboLogo = "
     __                     _            
    / /    __ _  _ __ ___  | |__    ___  
   / /    / _` || '_ ` _ \ | '_ \  / _ \ 
  / /___ | (_| || | | | | || |_) || (_) |
  \____/  \__,_||_| |_| |_||_.__/  \___/ 
                                       
";

    public function __construct(Command $console)
    {
        $this->console = $console;
    }

    /**
     * Display Lambo Logo
     *
     */
    public function displayLamboLogo(): void
    {
        foreach (explode("\n", $this->lamboLogo) as $line) {
            $this->console->info($line);
        }

        $this->console->alert("Super-powered 'laravel new' with Laravel and Valet.");
    }
}
