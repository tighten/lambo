<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class RunLaravelInstaller
{
    use AbortsCommands;

    protected $shell;
    private $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep("Running the Laravel installer");

        $process = $this->shell->execInRoot('laravel new ' . config('lambo.store.project_name') . $this->extraOptions());

        $this->abortIf(! $process->isSuccessful(), "The laravel installer did not complete successfully.", $process);

        $this->consoleWriter->verbose()->success($this->getFeedback());
    }

    public function extraOptions()
    {
        return sprintf('%s%s',
            config('lambo.store.dev') ? ' --dev' : '',
            ''
            /* @todo: while laravel installer is busted we must not use --quiet
            config('lambo.store.with_output') ? '' : ' --quiet' */
        );
    }

    public function getFeedback(): string
    {
        return sprintf("A new application '%s' has been created from the %s branch.",
            config('lambo.store.project_name'),
            config('lambo.store.dev') ? 'develop' : 'release'
        );
    }

}
