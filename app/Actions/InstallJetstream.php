<?php

namespace App\Actions;

use App\Actions\Concerns\InteractsWithComposer;
use App\Actions\Concerns\InteractsWithNpm;
use App\ConsoleWriter;
use App\LamboException;
use App\Shell;

class InstallJetstream
{
    use AbortsCommands;
    use InteractsWithComposer;
    use InteractsWithNpm;

    public const VALID_CONFIGURATIONS = [
        'inertia',
        'livewire',
        'inertia,teams',
        'livewire,teams',
        'teams,inertia',
        'teams,livewire'
    ];

    public const VALID_STACKS = [
        'Inertia' => 'inertia',
        'Livewire' => 'livewire',
    ];

    private $consoleWriter;
    private $shell;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        if (($stack = config('lambo.store.jetstream')) === false) {
            return;
        }

        if (! in_array($stack, self::VALID_CONFIGURATIONS, true)) {
            throw new LamboException("'{$stack}' is not a valid Jetstream configuration.");
        }

        $this->consoleWriter->logStep('Installing Laravel Jetstream starter kit');

        $this->composerRequire('laravel/jetstream');
        $this->installJetstream($stack);
        $this->installAndCompileNodeDependencies();

        $this->consoleWriter->success('Successfully installed Laravel Jetstream.');
    }

    protected function installJetstream(string $stack): void
    {
        $configuration = explode(',', $stack);
        $installJetstreamProcess = $this->shell->execInProject(sprintf(
            'php artisan jetstream:install %s%s%s',
            in_array('livewire', $configuration) ? 'livewire' : 'inertia',
            in_array('teams', $configuration) ? ' --teams' : '',
            config('lambo.store.with_output') ? '' : ' --quiet'
        ));
        $this->abortIf(! $installJetstreamProcess->isSuccessful(), 'Failed to install Laravel Jetstream.', $installJetstreamProcess);
    }
}
