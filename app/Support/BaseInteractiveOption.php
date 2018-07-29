<?php

namespace App\Support;

use App\Contracts\OptionContract;
use Symfony\Component\Process\ExecutableFinder;

abstract class BaseInteractiveOption implements OptionContract
{
    /**
     * The finder
     *
     * @var ExecutableFinder
     */
    protected $finder;

    public function __construct(ExecutableFinder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Store the answer in the singleton store.
     *
     * @param $key
     * @param $value
     * @return void
     */
    public function setLamboConfig(string $key, $value): void
    {
        if ($value === 'true') {
            $value = true;
        } elseif ($value === 'false') {
            $value = false;
        }

        config()->set("lambo.{$key}", $value);
    }
}
