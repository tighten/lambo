<?php

namespace App\Actions;

use App\Commands\NewCommand;
use App\Support\BaseAction;
use App\Support\ShellCommand;
use Symfony\Component\Process\ExecutableFinder;

class InstallNodeDependencies extends BaseAction
{
    /**
     * @var ExecutableFinder
     */
    protected $finder;

    /**
     * InstallNodeDependencies constructor.
     *
     * @param NewCommand $console
     * @param ShellCommand $shell
     * @param ExecutableFinder $finder
     */
    public function __construct(NewCommand $console, ShellCommand $shell, ExecutableFinder $finder)
    {
        parent::__construct($console, $shell);
        $this->finder = $finder;
    }

    /**
     * Install Node dependencies.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $directory = config('lambo.store.project_path');

        $node = config('lambo.config.node');

        if ($node === false) {
            return;
        }

        if ($node === 'npm') {
            if (! $this->finder->find($node)) {
                $this->console->error("Provided [{$node}] for installation, but couldn't find its executable.");
                return;
            }
            $this->console->info("Installing Node dependencies using: {$node}");
            $this->shell->inDirectory($directory, 'npm install');
            return;
        }

        if ($node === 'yarn') {
            if (!$this->finder->find($node)) {
                $this->console->error("Provided [{$node}] for installation, but couldn't find its executable.");
                return;
            }
            $this->console->info("Installing Node dependencies using: {$node}");
            $this->shell->inDirectory($directory, 'yarn');
            return;
        }

        if ($node === true) {
            if ($this->finder->find('yarn')) {
                $this->console->info('Installing Node dependencies using: yarn');
                $this->shell->inDirectory($directory, 'yarn');
                return;
            }
            if ($this->finder->find('npm')) {
                $this->console->info('Installing Node dependencies using: npm');
                $this->shell->inDirectory($directory, 'npm install');
                return;
            }

            $this->console->error("Config is set to node=true, but couldn't find yarn nor npm");
            return;
        }
    }
}
