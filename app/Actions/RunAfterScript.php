<?php

namespace App\Actions;

use App\Shell;
use Illuminate\Support\Facades\File;

class RunAfterScript
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        $afterScriptPath = config('home_dir') . '/.lambo/after';

        if (File::isFile($afterScriptPath)) {
            app('console-writer')->logStep('Running after script');

            $process = $this->shell->execInProject("sh " . $afterScriptPath);

            $this->abortIf(! $process->isSuccessful(), 'After file did not complete successfully', $process);

            app('console-writer')->success('After script has completed.');
        }
    }
}
