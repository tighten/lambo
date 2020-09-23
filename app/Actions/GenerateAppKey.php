<?php

namespace App\Actions;

use App\Shell;

class GenerateAppKey
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        app('console-writer')->logStep('Setting APP_KEY in .env');

        $process = $this->shell->execInProject("php artisan key:generate{$this->withQuiet()}");

        $this->abortIf(! $process->isSuccessful(), 'Failed to generated APP_KEY successfully', $process);

        app('console-writer')->verbose()->success('APP_KEY has been set.');
    }

    private function withQuiet()
    {
        return config('lambo.store.with_output') ? '' : ' --quiet';
    }
}
