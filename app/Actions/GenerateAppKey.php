<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;

class GenerateAppKey
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }

    public function __invoke()
    {
        $this->consoleWriter->logStep('Setting APP_KEY in .env');

        $process = $this->shell->execInProject("php artisan key:generate{$this->withQuiet()}");

        $this->abortIf(! $process->isSuccessful(), 'Failed to generated APP_KEY successfully', $process);

        $this->consoleWriter->success('APP_KEY has been set.');
    }
}
