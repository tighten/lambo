<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

class Release extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'dev';

    /**
     * Performs interactively.
     *
     * @param $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $options = [
            'false'   => 'Nope, I want the stable.',
            'true'    => 'I like living on the edge, take me to dev branch',
        ];

        $menuTitle = "Do you want to Laravel's dev branch?";

        $this->value = $console
            ->menu($menuTitle, $options)
            ->open();

        return $this;
    }
}
