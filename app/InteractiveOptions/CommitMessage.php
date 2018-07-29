<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

class CommitMessage extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'message';

    /**
     * Performs the option interactively.
     *
     * @param NewCommand $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $this->value = $console->anticipate('What should be the commit message?', ['Initial commit.']);

        return $this;
    }
}
