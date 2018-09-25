<?php

namespace App\Actions;

use App\Support\BaseAction;

class MergeInlineOptionsToConfig extends BaseAction
{
    /**
     * Merges command inline options to the current Lambo configuration. As usual, the config
     * gets loaded and merged with ~/.lambo/config.php in App\Providers\AppServiceProvider
     * and then gets overridden here with the user options provided at the command line.
     *
     * @return void
     */
    public function __invoke(): void
    {
        collect($this->console->options())
            ->reject(function ($item, $key) {
                return $item === null;
            })
            ->intersectByKeys($this->availableOptions())
            ->each(function ($item, $key) {
                if ($item === 'true') {
                    $item = true;
                } elseif ($item === 'false') {
                    $item = false;
                }

                config()->set("lambo.config.{$key}", $item);
            });
    }

    /**
     * Returns available options in Lambo config.
     *
     * @return array
     */
    protected function availableOptions(): array
    {
        return collect(config('lambo.config'))->all();
    }
}
