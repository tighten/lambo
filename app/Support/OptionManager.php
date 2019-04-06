<?php

namespace App\Support;

use App\Contracts\OptionContract;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

class OptionManager
{
    /** @var Collection  */
    private $options = [];

    /**
     * OptionManager constructor.
     *
     *
     */
    public function __construct()
    {
        $this->boot();
    }

    /**
     * Boot every option to internal state.
     *
     * @return void
     */
    private function boot(): void
    {
        $this->options = collect();

        $optionFiles = Finder::create()->in([
            app_path('Options')
        ]);

        foreach ($optionFiles as $optionFile) {
            $optionClass = "App\\Options\\" . str_replace('.php', '', basename($optionFile));

            if (is_subclass_of($optionClass, BaseOption::class)) {
                $option = new $optionClass;

                $this->options->put($option->getKey(), $option);
            }
        }
    }

    /**
     * Loads configuration, to all the options.
     *
     * @return void
     */
    public function load(): void
    {

    }

    /**
     * Get a specific option, by key.
     *
     * @param string $key
     * @return OptionContract
     */
    public function getOption(string $key): OptionContract
    {
        return $this->options->get($key);
    }

    /**
     * Every current option to customise.
     *
     * @return array
     */
    public function optionValuesForCustomisationMenu(): array
    {
        return $this->options
            ->map(function ($item, $key) {
                /** @var OptionContract $item */
                return $item->getTitle();
            })
            ->values()
            ->all();
    }

    /**
     * Every current option description and values ready to display.
     *
     * @return array
     */
    public function optionValuesForDisplay(): array
    {
        return $this->options
            ->map(function ($item, $key) {
                /** @var OptionContract $item */
                return [$item->getTitle(), $item->displayDescription(), $item->displayValue()];
            })
            ->all();
    }

    public function performOptionByTitle($console)
    {


    }
}
