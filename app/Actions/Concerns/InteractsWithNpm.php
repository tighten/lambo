<?php

namespace App\Actions\Concerns;

use App\Actions\AbortsCommands;
use App\Shell;

trait InteractsWithNpm
{
    use AbortsCommands;

    /**
     * @throws \App\LamboException
     */
    protected function installAndCompileNodeDependencies(): void
    {
        $this->installNodeDependencies();
        $this->compileNodeDependencies();
    }

    /**
     * @throws \App\LamboException
     */
    public function installNodeDependencies(): void
    {
        $process = app(Shell::class)->execInProject('npm install' . (config('lambo.store.with_output') ? '' : ' --silent'));
        $this->abortIf(! $process->isSuccessful(), 'Installation of npm dependencies did not complete successfully', $process);
    }

    /**
     * @throws \App\LamboException
     */
    protected function compileNodeDependencies(): void
    {
        $process = app(Shell::class)->execInProject('npm run build' . (config('lambo.store.with_output') ? '' : ' --silent'));
        $this->abortIf(! $process->isSuccessful(), 'Compilation of project assets did not complete successfully', $process);
    }
}
