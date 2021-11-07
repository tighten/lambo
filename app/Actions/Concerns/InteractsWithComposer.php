<?php

namespace App\Actions\Concerns;

use App\Actions\AbortsCommands;
use App\Shell;

trait InteractsWithComposer
{
    use AbortsCommands;

    protected function composerRequire(string $package, bool $forDev = true): void
    {
        $command = $this->getComposerRequireCommand($package, $forDev);
        $composerProcess = app(Shell::class)->execInProject($command);
        $this->abortIf(! $composerProcess->isSuccessful(), 'Composer package installation failed.', $composerProcess);
    }

    protected function getComposerRequireCommand(string $package, bool $forDev): string
    {
        return sprintf(
            'composer require %s%s%s',
            $package,
            $forDev ? ' --dev' : '',
            config('lambo.store.with_output') ? '' : ' --quiet'
        );
    }
}
