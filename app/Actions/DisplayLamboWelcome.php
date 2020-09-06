<?php

namespace App\Actions;

use App\ConsoleWriter;

class DisplayLamboWelcome
{
    protected $lamboLogo = "
     __                    __               :version:
    / /   ____ _____ ___  / /_  ____
   / /   / __ `/ __ `__ \/ __ \/ __ \
  / /___/ /_/ / / / / / / /_/ / /_/ /
 /_____/\__,_/_/ /_/ /_/_.___/\____/";

    protected $welcomeText = "
<info>Lambo:</info> Super-powered <comment>'laravel new'</comment> for Laravel and Valet.";

    private $consoleWriter;

    public function __construct(ConsoleWriter $consoleWriter)
    {
        $this->lamboLogo = str_replace(':version:', config('app.version'), $this->lamboLogo);
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        foreach (explode("\n", $this->lamboLogo) as $line) {
            // Extra space on the end fixes an issue with console when it ends with backslash
            $this->consoleWriter->text("<info>$line </info>");
        }

        foreach (explode("\n", $this->welcomeText) as $line) {
            // Extra space on the end fixes an issue with console when it ends with backslash
            $this->consoleWriter->text($line . " ");
        }
    }
}
