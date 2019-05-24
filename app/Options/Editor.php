<?php

namespace App\Options;

use App\Support\BaseOption;
use App\Support\OptionValue;

class Editor extends BaseOption
{
    public function bootOptionValues(): void
    {
        $this->addOptionValue('Sublime', 'subl');
    }

    public function getKey(): string
    {
        return 'editor';
    }

    public function getTitle(): string
    {
        return 'Editor';
    }

    public function displayDescription(): string
    {
        return 'The project will open in this editor';
    }

    public function bootStartingValue(): bool
    {
        $this->optionValue = $this->optionValues->first(function ($item, $key) {
            /** @var OptionValue $item */
            return $item->getValue() === config('lambo.config.editor');
        });

        return $this->optionValue !== null;
    }
}
