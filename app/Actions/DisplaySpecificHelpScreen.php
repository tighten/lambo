<?php

namespace App\Actions;

use App\GeneratesHelpScreens;
use Illuminate\Support\Str;

class DisplaySpecificHelpScreen
{
    use LamboAction, GeneratesHelpScreens;

    public function __invoke($target)
    {
        // @todo pass it to the correct render method
        $this->renderPresetHelp($target, $this->getObjectForTarget($target));
    }

    public function getObjectForTarget($target)
    {
        // if command existss with this target, then get it
        // if option exists for this target, get it
        // if preset exists for this target, get it
        $studly = Str::studly($target);
        // If this exists as a preset class, new it up
        return app(\App\Presets\Premade\Blueprint::class);
    }

    public function renderCommandHelp($target)
    {
        $this->line("<comment>Description:</comment>");
        $this->line("  Description for {$target} here.\n");
        $this->line("<comment>Usage:</comment>");
        $this->line("  lambo {$target} @todo do they take params");

        $this->renderOptions();
    }

    public function renderOptionHelp()
    {
        // @todo
    }

    public function renderPresetHelp($target, $preset)
    {
        // @todo allow for defining the params
        $this->line("<comment>Description:</comment>");
        $this->line("  " . $preset->description . "\n");
        $this->line("<comment>Usage:</comment>");
        $this->line("  lambo myApplication --presets=\"{$target}\"");

        $this->renderOptions();
    }
}
