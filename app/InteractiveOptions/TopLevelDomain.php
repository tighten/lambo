<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

class TopLevelDomain extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'tld';

    /**
     * Performs the option interactively.
     *
     * @param NewCommand $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $question = "What should be the TLD? .tld or tld - We've got you covered.";

        $this->value = $console->anticipate($question, ['test','app','dev','wip']);

        return $this;
    }
}
