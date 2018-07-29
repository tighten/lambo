<?php

namespace App\Interactive;

use App\Commands\NewCommand;
use Illuminate\Support\Collection;

class OptionManager
{
    /**
     * The interactive menu options.
     *
     * @var Collection
     */
    protected $interactiveMenuOptions;

    /**
     * The interactive options repository.
     *
     * @var OptionRepository
     */
    protected $optionRepository;

    /**
     * QuestionPerformer constructor.
     *
     * @param OptionRepository $optionRepository
     */
    public function __construct(OptionRepository $optionRepository)
    {
        $this->optionRepository = $optionRepository;
        $this->hydrateInteractiveMenuOptions();
    }

    /**
     * Hydrate interactive menu options.
     *
     * @return void
     */
    public function hydrateInteractiveMenuOptions(): void
    {
        $this->interactiveMenuOptions = $this->optionRepository->get();
    }

    /**
     * Get the interactive menu options.
     *
     * @return array
     */
    public function interactiveMenuOptions(): array
    {
        return $this->interactiveMenuOptions
            ->mapWithKeys(function ($item, $key) {
                return [ $item['key'] => $item['label'] ];
            })->all();
    }

    /**
     * Interactively performs the given option, by its key.
     *
     * @param string $optionKey
     * @param NewCommand $console
     * @return string
     */
    public function perform(string $optionKey, NewCommand $console): ?string
    {
        $option = $this->interactiveMenuOptions->firstWhere('key', $optionKey);

        $option = app($option['class'])->perform($console);

        if ($option->value() === null) {
            return null;
        }

        $this->setLamboConfig($option->key(), $option->value());

        return $option->value();
    }

    /**
     * Changes the key to value in Lambo config.
     *
     * @param string $key
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
