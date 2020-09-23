<?php

namespace App\Actions;

use App\Shell;
use Illuminate\Support\Facades\File;

class RunAfterScript
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
        $afterScriptPath = config('home_dir') . '/.lambo/after';
        if (! File::isFile($afterScriptPath)) {
            return;
        }

        app('console-writer')->logStep('Running after script');

        $process = $this->shell->execInProject("sh " . $afterScriptPath);
        $this->abortIf(! $process->isSuccessful(), 'After file did not complete successfully', $process);

        app('console-writer')->verbose()->success('After script has completed.');
    }
}
