<?php

namespace App\ActionsPreInstall;

use App\Support\BaseAction;

class PromptForCustomization extends BaseAction
{
    public const CUSTOMIZE_QUESTION = 'Would you like to (R)un with current config, or (C)ustomize? Or (E)xit.';

    /**
     * Prompts the user for runtime customization.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $answer = false;
        while (! in_array($answer, ['c','r','e'])) {
            $answer = strtolower($this->console->ask(self::CUSTOMIZE_QUESTION, 'r'));

            if ($answer === 'e') {
                $this->console->info("\nBye. Come back soon to build something awesome!\n");
                exit(1);
            }

            if ($answer === 'c') {
                app(CustomizeConfigRuntime::class, ['console' => $this->console])();
            }
        }
    }
}
