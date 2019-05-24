<?php

namespace App\Support;

use App\Contracts\OptionContract;
use App\Support\OptionValue;
use DomainException;

abstract class BaseOption implements OptionContract
{
    /** @var OptionValue */
    protected $optionValue;

    /** @var \Illuminate\Support\Collection  */
    protected $optionValues;

    protected $values = [];

    public function __construct()
    {
        $this->optionValues = collect();

        $this->bootOptionValues();
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function displayDescription(): string
    {
        return $this->description;
    }

    public function bootOptionValues(): void
    {
        foreach ($this->values as $display => $value) {
            $this->addOptionValue($display, $value);
        }
    }

    public function addOptionValue(string $title, $value): void
    {
        $this->optionValues[] = new OptionValue($title, $value);
    }

    public function getOptionValue()
    {
        return $this->optionValue->getValue();
    }

    public function setOptionValue(OptionValue $optionValue): void
    {
        $this->optionValue = $optionValue;
    }

    public function displayValue(): string
    {
        if (! $this->optionValue) {
            return '';
        }

        return $this->optionValue->getTitle();
    }

    public function getOptionValues(): \Illuminate\Support\Collection
    {
        return $this->optionValues;
    }

    public function bootStartingValue(): bool
    {
        $this->optionValue = $this->optionValues->first(function ($item, $key) {
            /** @var OptionValue $item */
            return $item->getValue() === config('lambo.config.' . $this->key);
        });

        return $this->optionValue !== null;
    }
}
