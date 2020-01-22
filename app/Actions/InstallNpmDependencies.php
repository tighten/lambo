<?php

namespace App\Actions;

use App\Shell\Shell;

class InstallNpmDependencies
{
    use LamboAction;

    protected $shell;

    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }

    public function __invoke()
    {
        if ($this->shouldNotRun()) {
            return;
        }

        if ($this->onlyMixSpecified()) {
            $this->warn('Installation of NPM dependencies was not specified but is required for asset compilation.');
        }

        $process = $this->shell->execInProject("npm install {$this->extraOptions()}");

        $this->abortIf(! $process->isSuccessful(), 'Installation of npm dependencies did not complete successfully', $process);

        $this->info('Npm dependencies installed.');
    }

    public function extraOptions()
    {
        return config('lambo.store.with-output') ? '' : '--silent';
    }

    protected function shouldNotRun()
    {
        return ! (config('lambo.store.node') || config('lambo.store.mix') || config('lambo.store.full'));
    }

    /**
     * @return bool
     */
    protected function onlyMixSpecified(): bool
    {
        return ! (config('lambo.store.node') || config('lambo.store.full'));
    }
}
