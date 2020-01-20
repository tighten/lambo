<?php

namespace App\Actions;

use App\Shell;

class InstallNpmDependencies
{
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
            app('console')->warn('[ lambo ] Installation of NPM dependencies was not specified but is required for asset compilation.');
        }

        $this->shell->execInProject("npm install {$this->extraOptions()}");
        app('console')->info('[ npm ] dependencies installed');
    }

    public function extraOptions()
    {
        return config('lambo.store.quiet') ? '--silent' : '';
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
