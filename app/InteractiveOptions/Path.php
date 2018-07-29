<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

class Path extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'path';

    /**
     * Performs the option interactively.
     *
     * @param NewCommand $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $question = 'New path for installation? (empty for current working dir)';

        $answer = $console->ask($question, 'cwd');

        if ($answer === 'cwd') {
            $this->value = $console->currentWorkingDir;
        } else {
            /**
             * @TODO verify that the provided path exists!
             */
            $this->value = $answer;
        }

        return $this;
    }
}
