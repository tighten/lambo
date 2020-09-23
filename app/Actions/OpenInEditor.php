<?php

namespace App\Actions;

use App\Shell;

class OpenInEditor
{
    use AbortsCommands;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if (config('lambo.store.no_editor')) {
            return;
        }

        app('console-writer')->logStep('Opening In Editor');

        $process = $this->shell->withTTY()->execInProject(sprintf("%s .", config('lambo.store.editor')));
        $this->abortIf(! $process->isSuccessful(), sprintf("Failed to open editor %s", config('lambo.store.editor')), $process);

        app('console-writer')->verbose()->success('Opening your project in ' . config('lambo.store.editor'));
    }
}
