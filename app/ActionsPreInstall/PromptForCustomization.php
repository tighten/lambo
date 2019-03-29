<?php

namespace App\ActionsPreInstall;

use function in_array;
use App\Support\BaseAction;

class PromptForCustomization extends BaseAction
{
    /**
     * Prompts the user for runtime customization.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $customizeQuestion = 'Would you like to (R)un with current config, or (C)ustomize? Or (E)xit.';

        $answer = false;
        while (! in_array($answer, ['c','r','e'])) {
            $answer = strtolower($this->console->ask($customizeQuestion, 'r'));

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
