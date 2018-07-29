<?php

namespace App\Actions;

use App\Support\BaseAction;

class MergeOptionsToConfig extends BaseAction
{
    /**
     * Merges command inline options to the current Lambo configuration.
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

                config()->set("lambo.{$key}", $item);
            });
    }

    /**
     * Returns available options in Lambo config.
     *
     * @return array
     */
    protected function availableOptions(): array
    {
        return collect(config('lambo'))->all();
    }
}
