<?php

namespace App\Interactive;

use App\InteractiveOptions\Auth;
use App\InteractiveOptions\CreateDatabase;
use App\InteractiveOptions\Path;
use App\InteractiveOptions\TopLevelDomain;
use Illuminate\Support\Collection;
use App\InteractiveOptions\Editor;
use App\InteractiveOptions\Browser;
use App\InteractiveOptions\Release;
use App\InteractiveOptions\ValetLink;
use App\InteractiveOptions\CommitMessage;
use App\InteractiveOptions\NodeDependencies;

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
     * The interactive options.
     *
     * @return Collection
     */
    public function interactiveOptions(): Collection
    {
        return collect([
            [
                'key' => 'editor',
                'label' => 'Editor - to open project after installation',
                'class' => Editor::class,
            ],
            [
                'key' => 'message',
                'label' => 'The commit message',
                'class' => CommitMessage::class,
            ],
            [
                'key' => 'path',
                'label' => 'Installation path',
                'class' => Path::class,
            ],
            [
                'key' => 'dev',
                'label' => 'The Laravel branch to use, dev or stable',
                'class' => Release::class,
            ],
            [
                'key' => 'auth',
                'label' => "Laravel's Auth scaffolding (auth:make)",
                'class' => Auth::class,
            ],
            [
                'key' => 'node',
                'label' => 'Install Node dependencies',
                'class' => NodeDependencies::class,
            ],
            [
                'key' => 'browser',
                'label' => 'Open the project in the browser',
                'class' => Browser::class,
            ],
            [
                'key' => 'link',
                'label' => 'Valet link',
                'class' => ValetLink::class,
            ],
            [
                'key' => 'tld',
                'label' => 'Top Level Domain',
                'class' => TopLevelDomain::class,
            ],
            [
                'key' => 'database',
                'label' => 'Create a new database',
                'class' => CreateDatabase::class,
            ],
        ]);
    }

    /**
     * Returns the interactive options.
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
