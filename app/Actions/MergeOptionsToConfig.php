<?php

namespace App\Actions;

use App\Support\BaseAction;

class MergeOptionsToConfig extends BaseAction
{
    public function __invoke()
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

    protected function availableOptions(): array
    {
        return collect(config('lambo'))->all();
    }
}
