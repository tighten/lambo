<?php

namespace App\ActionsPreInstall;

use App\Support\BaseAction;

class CustomiseConfigRuntime extends BaseAction
{
    /**
     * Customise the configuration in runtime.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $choices = [
            'Auth scaffolding',
            'Open the browser on your new application.',
            'Customise the first commit message',
        ];

        $title = 'Which configuration to setup?';

        $option = $this->console->choice($title, $choices);

        if ($option === null) {
            $this->console->initialScreen('Nothing was changed. Ready to go?', 'alert');
            return;
        }

        /*
         * Perform the new 5.8 Option, which may have a Verification (to assure what user selected)
         */

        $this->console->initialScreen();
    }
}
