<?php

namespace App\Interactive;

use App\InteractiveOptions\Path;
use Illuminate\Support\Collection;
use App\InteractiveOptions\Editor;
use App\InteractiveOptions\Release;
use App\InteractiveOptions\CommitMessage;

class OptionRepository
{
    /**
     * Available Lambo config options.
     *
     * @var Collection
     */
    protected $availableConfigOptions;

    /**
     * OptionRepository constructor.
     *
     */
    public function __construct()
    {
        $this->hydrateAvailableConfigOptions();
    }

    /**
     * The interactive options
     *
     * @return Collection
     */
    public function interactiveOptions(): Collection
    {
        return collect([
            [
                'key'   => 'editor',
                'label' => 'Editor - to open project after installation',
                'class' => Editor::class,
            ],
            [
                'key'   => 'message',
                'label' => 'The commit message',
                'class' => CommitMessage::class,
            ],
            [
                'key'   => 'path',
                'label' => 'Installation path',
                'class' => Path::class,
            ],
            [
                'key'   => 'dev',
                'label' => 'The Laravel branch to use, dev or stable',
                'class' => Release::class,
            ],
        ]);
    }

    /**
     * Returns he interactive options
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->interactiveOptions()
            ->filter(function ($item, $key) {
                return $this->availableConfigOptions->contains($item['key']);
            });
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
}
