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
     * The chosen value, or retrieved input.
     *
     * @string
     */
    protected $value;

    /**
     * Performs interactively.
     *
     * @param $console
     * @return null|string
     */
    public function perform(NewCommand $console): ?string
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

        $value = $console
            ->menu('Choose the editor to open in', $options->all())
            ->open();

        if ($value === null) {
            return null;
        }

        $this->setLamboConfig($this->key, $value);

        return $value;
    }
}
