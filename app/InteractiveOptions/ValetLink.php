<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

class ValetLink extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'link';

    /**
     * Performs interactively.
     *
     * @param $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $menuTitle = 'Valet link the project folder?';

        $options = [
            'true'    => 'Yes',
            'false'   => 'No',
        ];

        $this->value = $console
            ->menu($menuTitle, $options)
            ->open();

        return $this;
    }
}
