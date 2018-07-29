<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

class CreateDatabase extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'database';

    /**
     * Performs interactively.
     *
     * @param $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $menuTitle = 'Create the database for you?';

        $options = [
            'false'     => 'No',
            'mysql'     => 'Yes. MySQL',
            'sqlite'    => 'Yes. SQLite',
        ];

        $this->value = $console
            ->menu($menuTitle, $options)
            ->open();

        return $this;
    }
}
