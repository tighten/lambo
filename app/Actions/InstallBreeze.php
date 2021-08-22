<?php

namespace App\Actions;

use App\Actions\Concerns\InteractsWithComposer;
use App\Actions\Concerns\InteractsWithNpm;
use App\ConsoleWriter;
use App\LamboException;
use App\Shell;

class InstallBreeze
{
    use AbortsCommands;
    use InteractsWithComposer;
    use InteractsWithNpm;

    public const VALID_STACKS = [
        'Blade' => 'blade',
        'Vue' => 'vue',
        'React' => 'react',
    ];

    private $shell;
    private $consoleWriter;

    public function __construct(Shell $shell, ConsoleWriter $consoleWriter)
    {
        $this->shell = $shell;
        $this->consoleWriter = $consoleWriter;
    }

    public function __invoke()
    {
        if (($stack = config('lambo.store.breeze')) === false) {
            return;
        }

        if (! in_array($stack, array_values(self::VALID_STACKS), true)) {
            throw new LamboException("'{$stack}' is not a valid Breeze configuration.");
        }

        $this->consoleWriter->logStep('Installing Laravel Breeze starter kit');

        $this->composerRequire('laravel/breeze');
        $this->installBreeze($stack);
        $this->installAndCompileNodeDependencies();

        $this->consoleWriter->success('Successfully installed Laravel Breeze.');
    }

    protected function installBreeze($stack): void
    {
        $process = $this->shell->execInProject(sprintf(
            'php artisan breeze:install%s%s',
            $stack === 'blade' ? '' : " {$stack}",
            config('lambo.store.with_output') ? '' : ' --quiet'
        ));
        $this->abortIf(! $process->isSuccessful(), 'Failed to install Laravel Breeze.', $process);
    }
}
