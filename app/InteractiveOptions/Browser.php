<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

class Browser extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'browser';

    /**
     * Performs interactively.
     *
     * @param $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $menuTitle = 'Open the project in the browser?';

        $options = collect([
            'false' => 'No, thanks.',
            'true' => 'Yes.',
        ])->filter(function ($item, $key) {
            return $this->finder->find($key) !== null;
        })->put('false', 'Do not open.');

        $this->value = $console
            ->menu($menuTitle, $options->all())
            ->open();

        return $this;
    }
}
