<?php

namespace App\Actions;

use App\Commands\Debug;

class ValidateConfiguration
{
    use Debug;

    protected $consoleWriter;

    public function __invoke()
    {
        app('console-writer')->logStep('Validating configuration');

        config(['lambo.store.frontend' => $this->getFrontendConfiguration()]);
        $this->checkTeamsConfiguration();

        app('console-writer')->verbose()->success('Configuration is valid.');

        if (app('console-writer')->isDebug()) {
            $this->debugReport();
        }
    }

    private function getFrontendConfiguration(): string
    {
        $inertia = config('lambo.store.inertia');
        $livewire = config('lambo.store.livewire');

        if ($inertia && $livewire) {
            return $this->chooseBetweenFrontends();
        }

        if (! $inertia && ! $livewire) {
            return 'none';
        }

        return $inertia ? 'inertia' : 'livewire';
    }

    private function chooseBetweenFrontends()
    {
        app('console-writer')->warn('inertia and livewire cannot be used together. ');

        $options = [
            'use inertia' => 'inertia',
            'use livewire' => 'livewire',
            "continue without frontend scaffolding" => 'none',
        ];
        $choice = app('console')->choice('What would you like to do?', array_keys($options), 2);

        app('console-writer')->verbose()->ok($choice);

        return $options[$choice];
    }

    private function checkTeamsConfiguration()
    {
        if ((config('lambo.store.frontend') === 'none') && config('lambo.store.teams')) {
            app('console-writer')->verbose()->note('You specified --teams but neither inertia or livewire are being used. Skipping...');
        }
    }

    protected function debugReport(): void
    {
        app('console-writer')->panel('Debug', 'Start', 'fg=black;bg=white');

        app('console-writer')->text([
            'Configuration may have changed after validation',
            'Configuration is now as follows:',
        ]);
        $this->configToTable();

        app('console-writer')->panel('Debug', 'End', 'fg=black;bg=white');
    }
}
