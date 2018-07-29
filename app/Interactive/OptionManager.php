<?php

namespace App\Interactive;

use App\Options\Editor;
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
     * Available Lambo config options.
     *
     * @var Collection
     */
    protected $availableConfigOptions;

    /**
     * QuestionPerformer constructor.
     *
     */
    public function __construct()
    {
        $this->hydrateAvailableConfigOptions();
        $this->hydrateInteractiveMenuOptions();
    }

    /**
     * Hydrate interactive menu options.
     *
     * @return void
     */
    public function hydrateInteractiveMenuOptions(): void
    {
        $this->interactiveMenuOptions = collect([
            [
                'key'   => 'editor',
                'label' => 'Editor - to open project after installation',
                'class' => Editor::class,
            ]
        ]);
    }

    /**
     * Hydrate the available config options.
     *
     * @return void
     */
    protected function hydrateAvailableConfigOptions(): void
    {
        $this->availableConfigOptions = collect(config('lambo'))->keys();
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

        return app($option['class'])->perform($console);
    }
}
