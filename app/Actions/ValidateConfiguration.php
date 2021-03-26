<?php

namespace App\Actions;

use App\Commands\Debug;
use App\ConsoleWriter;

class ValidateConfiguration
{
    use Debug;

    protected $consoleWriter;

    public function __construct(ConsoleWriter $consoleWriter)
    {
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep('Validating configuration');

        config(['lambo.store.frontend' => $this->getFrontendConfiguration()]);
        $this->checkTeamsConfiguration();

        $this->consoleWriter->success('Configuration is valid.');

        if ($this->consoleWriter->isDebug()) {
            $this->debugReport();
        }
    }

    protected function debugReport(): void
    {
        $this->consoleWriter->panel('Debug', 'Start', 'fg=black;bg=white');

        $this->consoleWriter->text([
            'Configuration may have changed after validation',
            'Configuration is now as follows:',
        ]);
        $this->configToTable();

        $this->consoleWriter->panel('Debug', 'End', 'fg=black;bg=white');
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
        $this->consoleWriter->warn('inertia and livewire cannot be used together. ');

        $options = [
            'use inertia' => 'inertia',
            'use livewire' => 'livewire',
            'continue without frontend scaffolding' => 'none',
        ];
        $choice = app('console')->choice('What would you like to do?', array_keys($options), 2);

        $this->consoleWriter->ok($choice);

        return $options[$choice];
    }

    private function checkTeamsConfiguration()
    {
        if ((config('lambo.store.frontend') === 'none') && config('lambo.store.teams')) {
            $this->consoleWriter->note('You specified --teams but neither inertia or livewire are being used. Skipping...');
        }
    }
}
