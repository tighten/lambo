<?php

namespace App\Options;

use App\Support\BaseOption;
use App\Support\OptionValue;

class DevBranch extends BaseOption
{
    public function bootOptionValues(): void
    {
        $this->addOptionValue('No', false);
        $this->addOptionValue('Yes', true);
    }

    public function getKey(): string
    {
        return 'dev';
    }

    public function getTitle(): string
    {
        return 'Dev Branch';
    }

    public function displayDescription(): string
    {
        return 'Use development branch?';
    }

    public function bootStartingValue(): bool
    {
        $this->optionValue = $this->optionValues->first(function ($item, $key) {
            /** @var OptionValue $item */
            return $item->getValue() === config('lambo.config.dev');
        });

        return $this->optionValue !== null;
    }
}
