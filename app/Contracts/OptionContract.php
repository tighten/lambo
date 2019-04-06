<?php

namespace App\Contracts;

interface OptionContract
{
    public function getKey(): string;

    public function getTitle(): string;

    public function displayDescription(): string;

    public function displayValue(): string;

    public function getOptionValue();

    public function bootStartingValue(): bool;
}
