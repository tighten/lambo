<?php

namespace Tests\Unit\Fakes;

use Exception;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

class FakeInput implements InputInterface
{
    private $input;
    private $parameterOptions;

    public function __construct(array $input = [])
    {
        $this->input = $input;
        $this->parameterOptions = collect($input)->mapWithKeys(function ($value, $key) {
            return ["--{$key}" => $value];
        })->toArray();
    }

    public function getFirstArgument()
    {
        throw new Exception('getFirstArgument() has not been implemented.');
    }

    public function hasParameterOption($values, bool $onlyParams = false)
    {
        return Arr::has($this->parameterOptions, (array) $values);
    }

    public function getParameterOption($values, $default = false, bool $onlyParams = false)
    {
        throw new Exception('getParameterOption() has not been implemented.');
    }

    public function bind(InputDefinition $definition)
    {
        throw new Exception('bind() has not been implemented.');
    }

    public function validate()
    {
        throw new Exception('validate() has not been implemented.');
    }

    public function getArguments()
    {
        throw new Exception('getArguments() has not been implemented.');
    }

    public function getArgument(string $name)
    {
        throw new Exception('getArgument() has not been implemented.');
    }

    public function setArgument(string $name, $value)
    {
        throw new Exception('setArgument() has not been implemented.');
    }

    public function hasArgument(string $name)
    {
        throw new Exception('hasArgument() has not been implemented.');
    }

    public function getOptions()
    {
        return $this->input;
    }

    public function getOption(string $name)
    {
        throw new Exception('getOption() has not been implemented.');
    }

    public function setOption(string $name, $value)
    {
        throw new Exception('setOption() has not been implemented.');
    }

    public function hasOption(string $name)
    {
        throw new Exception('hasOption() has not been implemented.');
    }

    public function isInteractive()
    {
        throw new Exception('isInteractive() has not been implemented.');
    }

    public function setInteractive(bool $interactive)
    {
        throw new Exception('setInteractive() has not been implemented.');
    }
}
