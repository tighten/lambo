<?php

namespace App\Support;

class OptionValue
{
    /** @var string */
    private $title;

    /** @var mixed */
    private $value;

    /**
     * OptionValue constructor.
     *
     * @param string $title
     * @param $value
     */
    public function __construct(string $title, $value)
    {
        $this->title = $title;

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
