<?php

namespace App\Actions;

use App\Support\BaseAction;

class MergeOptionsToConfig extends BaseAction
{
    public function __invoke()
    {
        $options = collect($this->console->options())
            ->intersectByKeys($this->availableOptions());

        $options->each(function ($item, $key) {

            if ($item === 'true') {
                $item = true;
            }
            elseif ($item === 'false') {
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