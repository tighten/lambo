<?php

namespace App\Actions;

use App\Support\BaseAction;

class DisplayCurrentConfiguration extends BaseAction
{
    /**
     * Displays current configuration.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $rows = config('lambo.config', []);

        $rows = collect($rows)->reject(function ($item, $key) {
            return $key === 'after';
        })->map(function ($item, $key) {

            $item = $this->translateItemValueToDisplay($item, $key);

            return [
                $this->headerConfiguration() => $key,
                $this->headerValue() => $item,
            ];
        })->all();

        $this->console->table([$this->headerConfiguration(), $this->headerValue()], $rows);
    }

    /**
     * The translation for configuration's name, on the Table Header
     *
     * @return string
     */
    protected function headerConfiguration(): string
    {
        return 'Configuration';
    }

    /**
     * The translation for configuration's value, on the Table Header
     *
     * @return string
     */
    protected function headerValue(): string
    {
        return 'Value';
    }

    /**
     * Return the string to display on screen, based on the provided params.
     *
     * @param $item
     * @param $key
     * @return string
     */
    protected function translateItemValueToDisplay($item, $key)
    {
        if (is_bool($item)) {
            $item = $item ? 'true' : 'false';
        }

        if (is_string($item) && $item === '') {
            $item = '(empty)';
        }

        if ($key === 'db_password') {
            $item = '[***password***]';
        }

        return $item;
    }
}
