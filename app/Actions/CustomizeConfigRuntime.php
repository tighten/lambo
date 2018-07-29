<?php

namespace App\Actions;

use App\Facades\Options;
use App\Support\BaseAction;

class CustomizeConfigRuntime extends BaseAction
{
    /**
     * Customize the configuration in runtime.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $option = $this->console
            ->menu('Change configuration value', Options::interactiveMenuOptions())
            ->open();

        $nothingChanged = ['Nothing was changed. Ready to go?', 'alert'];
        [$message, $level ] = $nothingChanged;

        if ($option !== null) {
            $selection = Options::perform($option, $this->console);

            if ($selection->value() !== null) {
                $message    = "You have set the configuration [{$option}] to: {$selection->value()}";
                $level      = 'info';
            } else {
                $message    = $selection->message();
                $level      = $selection->messageLevel();
            }
        }

        $this->console->initialScreen($message, $level);
    }
}
