<?php

namespace App\Actions;

use App\ConsoleWriter;

class ValidateConfiguration
{
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

        $this->consoleWriter->verbose()->success('Configuration is valid.');
    }

    protected function getFrontendConfiguration(): string
    {
        if ($configuration = $this->validateFrontendConfiguration()) {
            return $configuration;
        }

        $this->consoleWriter->warn('inertia and livewire cannot be used together. ');
        $options = [
            'use inertia' => 'inertia',
            'use livewire' => 'livewire',
            "continue without frontend scaffolding" => 'none',
        ];

        $choice = app('console')->choice('What would you like to do?', array_keys($options), 2);

        $this->consoleWriter->verbose()->ok($choice);

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
            $this->consoleWriter->verbose()->note('You specified --teams but neither inertia or livewire are being used. Skipping...');
        }
    }

    private function checkDatabaseConfiguration()
    {
    }
}
