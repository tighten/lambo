<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

class Auth extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'auth';

    /**
     * Performs interactively.
     *
     * @param $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $menuTitle = "Perform Laravel's Auth scaffolding (auth:make)";

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
