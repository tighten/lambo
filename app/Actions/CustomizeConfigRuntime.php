<?php

namespace App\Actions;

use App\Support\BaseAction;
use App\Commands\NewCommand;
use App\Support\ShellCommand;
use Illuminate\Support\Collection;

class CustomizeConfigRuntime extends BaseAction
{
    /**
     * Available Lambo config options.
     *
     * @var Collection
     */
    protected $availableOptions;

    /**
     * CustomizeConfigRuntime constructor.
     *
     * @param NewCommand $console
     * @param ShellCommand $shell
     */
    public function __construct(NewCommand $console, ShellCommand $shell)
    {
        parent::__construct($console, $shell);

        $this->hydrateAvailableOptions();
    }

    /**
     * Customize the configuration in runtime.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $option = $this->console->menu('Change configuration value', $this->availableOptions->all())->open();

        /**
         * @TODO Implement (or refactoring) the App\Questions classes, so that boolean options, or string input, etc
         *
         */

        $message = "You have chosen the option: [{$option}]. Runtime changes still not implemented.";
        $level = 'alert';

        $this->console->initialScreen($message, $level);
    }

    /**
     * Hydrate the available options.
     *
     * @return void
     */
    protected function hydrateAvailableOptions(): void
    {
        $this->availableOptions = collect(config('lambo'))
            ->mapWithKeys(function ($item, $key) {
                $keyTitle = str_replace('_', ' ', $key);
                $keyTitle = ucwords($keyTitle);

                return [$key => $keyTitle];
            });
    }
}
