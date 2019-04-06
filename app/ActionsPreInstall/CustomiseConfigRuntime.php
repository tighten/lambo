<?php

namespace App\ActionsPreInstall;

use App\Support\BaseAction;
use App\Facades\OptionManager;

class CustomiseConfigRuntime extends BaseAction
{
    protected const EXIT_MESSAGE = 'Exit without changes.';

    /**
     * Customise the configuration in runtime.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $choices = OptionManager::optionValuesForCustomisationMenu();

        $choices = array_merge(
            [self::EXIT_MESSAGE],
            $choices
        );

        $title = 'Which configuration to setup?';

        $option = $this->console->choice($title, $choices);

        if ($option === self::EXIT_MESSAGE) {
            $this->console->initialScreen('Nothing was changed. Ready to go?', 'alert');
            return;
        }

        $this->console->initialScreen();
    }
}
