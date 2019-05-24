<?php

namespace App\Support;

use App\Contracts\OptionContract;
use DomainException;

abstract class BaseOption implements OptionContract
{
    /** @var OptionValue */
    protected $optionValue;

    /** @var \Illuminate\Support\Collection  */
    protected $optionValues;

    public function __construct()
    {
        $this->optionValues = collect();

        $this->bootOptionValues();
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

    public function bootOptionValues(): void
    {
        throw new DomainException('Implement method in Option class.');
    }

    public function getOptionValues(): \Illuminate\Support\Collection
    {
        return $this->optionValues;
    }
}
