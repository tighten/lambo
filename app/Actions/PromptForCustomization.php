<?php

namespace App\Actions;

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
        while (!collect(['c','C','r','R','e','E'])->contains($answer)) {
            $answer = $this->console->ask($customizeQuestion);

            if (collect(['e','E'])->contains($answer)) {
                $this->console->info('Bye. Come back soon to build something awesome!');
                exit(1);
            }

            if (collect(['c','C'])->contains($answer)) {
                $this->console->alert('Still not all questions are implemented.');
                app(AskConfigQuestions::class, ['console' => $this->console])();
            }
        }
    }
}
