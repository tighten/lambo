<?php

namespace App\ActionsPreInstall;

use App\Contracts\OptionContract;
use App\Support\BaseAction;
use App\Facades\OptionManager;
use App\Support\OptionValue;

class CustomiseConfigRuntime extends BaseAction
{
    public const EXIT_MESSAGE = 'Exit without changes.';

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

        $choice = $this->console->choice($title, $choices);

        if ($choice === static::EXIT_MESSAGE) {
            $this->console->initialScreen('Nothing was changed. Ready to go?', 'alert');
            return;
        }

        $this->performOption(OptionManager::getOptionByTitle($choice));

        $this->console->initialScreen();
    }

    /**
     * Performs the selection for the chosen option.
     *
     * @param OptionContract $option
     */
    private function performOption($option): void
    {
        /** @var OptionContract $option */
        $choices = $option->getOptionValues()
            ->map(function (OptionValue $item, $key) {
                return $item->getTitle();
            })
            ->values()
            ->all();

        $choice = $this->console->choice($option->displayDescription(), $choices);

        $choiceOptionValue = $option->getOptionValues()
            ->first(function (OptionValue $item, $key) use ($choice) {
                return $item->getTitle() === $choice;
            });

        $option->setOptionValue($choiceOptionValue);
    }
}
