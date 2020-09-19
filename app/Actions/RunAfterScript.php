<?php

namespace App\Actions;

use App\ConsoleWriter;
use App\Shell;
use Illuminate\Support\Facades\File;

class RunAfterScript
{
    use AbortsCommands;

    protected $shell;
    protected $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        $afterScriptPath = config('home_dir') . '/.lambo/after';

        if (File::isFile($afterScriptPath)) {
            $this->consoleWriter->logStep('Running after script');

            $process = $this->shell->execInProject("sh " . $afterScriptPath);

            $this->abortIf(! $process->isSuccessful(), 'After file did not complete successfully', $process);

            $this->consoleWriter->verbose()->success('After script has completed.');
        }
    }
}
