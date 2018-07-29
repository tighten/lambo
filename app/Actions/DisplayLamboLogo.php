<?php

namespace App\Actions;

use App\Support\BaseAction;

class DisplayLamboLogo extends BaseAction
{
    protected $lamboLogo = "
     __                     _            
    / /    __ _  _ __ ___  | |__    ___  
   / /    / _` || '_ ` _ \ | '_ \  / _ \ 
  / /___ | (_| || | | | | || |_) || (_) |
  \____/  \__,_||_| |_| |_||_.__/  \___/ 
                                       
";

    /**
     * Displays the Lambo logo.
     *
     * @return void
     */
    public function __invoke(): void
    {
        foreach (explode("\n", $this->lamboLogo) as $line) {
            $this->console->info($line);
        }

        $this->console->alert("Super-powered 'laravel new' with Laravel and Valet.");
    }
}
