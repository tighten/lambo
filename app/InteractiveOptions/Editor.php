<?php

namespace App\InteractiveOptions;

use App\Commands\NewCommand;
use App\Support\BaseInteractiveOption;

class Editor extends BaseInteractiveOption
{
    /**
     * Option key.
     *
     * @var string
     */
    protected $key = 'editor';

    /**
     * Performs interactively.
     *
     * @param $console
     * @return BaseInteractiveOption
     */
    public function perform(NewCommand $console): BaseInteractiveOption
    {
        $options = collect([
            'pstorm'    => 'PHPStorm',
            'subl'      => 'Sublime Text',
            'sublime'   => 'Sublime-Text',
            'code'      => 'Visual Studio Code',
            'yadayada'  => 'Nonexisting',
        ])->filter(function ($item, $key) {
            return $this->finder->find($key) !== null;
        })->put('false', 'Do not open.');

        $menuTitle = 'Choose the editor to open in';

        $this->value = $console
            ->menu($menuTitle, $options->all())
            ->open();

        return $this;
    }
}
