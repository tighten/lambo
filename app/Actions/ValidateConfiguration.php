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

            app('console-writer')->panel('Debug', 'Start', 'fg=black;bg=white');

            app('console-writer')->text([
                'Configuration may have changed after validation',
                'Configuration is now as follows:',
            ]);
            $this->configToTable();

            app('console-writer')->panel('Debug', 'End', 'fg=black;bg=white');
        }
    }

    protected function getFrontendConfiguration(): string
    {
        if ($configuration = $this->validateFrontendConfiguration()) {
            return $configuration;
        }

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

    protected function validateFrontendConfiguration()
    {
        $inertia = config('lambo.store.inertia');
        $livewire = config('lambo.store.livewire');

        if ($inertia xor $livewire) {
            return $inertia ? 'inertia' : 'livewire';
        }

        if (! ($inertia && $livewire)) {
            return 'none';
        }

        return false;
    }

    protected function checkTeamsConfiguration()
    {
        if ((config('lambo.store.frontend') === 'none') && config('lambo.store.teams')) {
            app('console-writer')->verbose()->note('You specified --teams but neither inertia or livewire are being used. Skipping...');
        }
    }
}
