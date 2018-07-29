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

        /**
         * @TODO With the app container, or the contained executable, can't be found.
         */
        $options = collect([
            'false'                                                             => 'No, thanks',
            'true'                                                              => 'Yes. With valet open command',
            '/Applications/Firefox.app/Contents/MacOS/firefox'                  => 'Firefox',
            '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome'    => 'Google Chrome',
            '/Applications/Safari.app/Contents/MacOS/Safari'                    => 'Safari',
            '/Applications/Opera.app/Contents/MacOS/Opera'                      => 'Opera',
        ])->filter(function ($item, $key) {
            return $this->finder->find($key) !== null;
        })->put('false', 'Do not open.');

        $this->value = $console
            ->menu($menuTitle, $options->all())
            ->open();

        return $this;
    }
}
