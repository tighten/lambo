<?php

namespace App\Contracts;

use App\Support\OptionValue;

interface OptionContract
{
    public function getKey(): string;

    public function getTitle(): string;

    public function displayDescription(): string;

    public function displayValue(): string;

    public function getOptionValue();

    public function setOptionValue(OptionValue $optionValue): void;

    public function bootStartingValue(): bool;

    public function getOptionValues(): \Illuminate\Support\Collection;
}
