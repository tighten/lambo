<?php

namespace Tests\Feature;

use App\Actions\InstallJetstream;
use App\LamboException;
use App\Shell;
use Tests\TestCase;

/**
 * @group front-end-scaffolding
 */
class InstallJetstreamTest extends TestCase
{
    /**
     * @test
     * @throws LamboException
     */
    function it_installs_laravel_jetstream()
    {
        foreach ([false, true] as $withOutput) {
            foreach ([false, true] as $useTeams) {
                foreach (InstallJetstream::VALID_STACKS as $stack) {
                    config(['lambo.store.jetstream' => $stack]);
                    config(['lambo.store.with_output' => $withOutput]);

                    if ($this->isDebug()) {
                        $this->logUseCase($stack, $useTeams, $withOutput);
                    }

                    $this->shouldExecInProject($this->getComposerCommand($withOutput));
                    $this->shouldExecInProject($this->getJetstreamInstallCommand($stack, $withOutput));
                    $this->shouldExecInProject($this->getNpmInstallCommand($withOutput));
                    $this->shouldExecInProject($this->getCompileAssetsCommand($withOutput));

                    app(InstallJetstream::class)();

                    if ($this->isDebug()) {
                        $this->toSTDOUT("\n ✔ PASS\n");
                    }
                }
            }
        }
    }

    /**
     * @test
     * @throws LamboException
     */
    function it_skips_jetstream_installation()
    {
        $this->spy(Shell::class);
        config(['lambo.store.jetstream' => false]);

        app(InstallJetstream::class)();

        $this->shell->shouldNotHaveReceived('execInProject');
    }

    /** @test */
    function it_throws_a_lambo_exception_if_an_invalid_jetstream_stack_is_requested()
    {
        config(['lambo.store.jetstream' => null]);
        $this->expectException(LamboException::class);
        app(InstallJetstream::class)();

        config(['lambo.store.jetstream' => '']);
        $this->expectException(LamboException::class);
        app(InstallJetstream::class)();

        config(['lambo.store.jetstream' => 'invalid']);
        $this->expectException(LamboException::class);
        app(InstallJetstream::class)();

        config(['lambo.store.jetstream' => true]);
        $this->expectException(LamboException::class);
        app(InstallJetstream::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_composer_installation_fails()
    {
        config(['lambo.store.jetstream' => 'inertia']);

        $this->shouldExecInProjectAndFail('composer require laravel/jetstream --dev --quiet');
        $this->expectException(LamboException::class);

        app(InstallJetstream::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_jetstream_installation_fails()
    {
        config(['lambo.store.jetstream' => 'inertia']);

        $this->shouldExecInProject('composer require laravel/jetstream --dev --quiet');
        $this->shouldExecInProjectAndFail('php artisan jetstream:install inertia --quiet');
        $this->expectException(LamboException::class);

        app(InstallJetstream::class)();
    }

    private function getJetstreamInstallCommand(string $stack, $showOutput): string
    {
        return "php artisan jetstream:install {$stack}" . ($showOutput ? '' : ' --quiet');
    }

    private function getNpmInstallCommand($showOutput): string
    {
        return 'npm install' . ($showOutput ? '' : ' --silent');
    }

    private function getComposerCommand(bool $withOutput = false): string
    {
        return 'composer require laravel/jetstream --dev' . ($withOutput ? '' : ' --quiet');
    }

    private function getCompileAssetsCommand($withOutput): string
    {
        return 'npm run build' . ($withOutput ? '' : ' --silent');
    }

    private function logUseCase(string $stack, $useTeams, $showOutput): void
    {
        $useTeamsStr = $useTeams ? 'true' : 'false';
        $showOutputStr = $showOutput ? 'true' : 'false';

        $configStack = config('lambo.store.jetstream');
        $configShowOutputStr = config('lambo.store.show_output') ? 'true' : 'false';

        $this->toSTDOUT("────────────────────────────\n");
        $this->toSTDOUT(sprintf(
            " USE CASE\n   lambo new <project> %s--jetstream=%s%s",
            $showOutput ? '-v[vv] ' : '',
            $stack,
            $useTeams ? ',teams' : ''
        ));
        $this->toSTDOUT(implode(PHP_EOL, [
            "   1. {$this->getComposerCommand($showOutput)}",
            "   2. {$this->getJetstreamInstallCommand($stack, $showOutput)}",
            "   3. {$this->getNpmInstallCommand($showOutput)}",
            "   4. {$this->getCompileAssetsCommand($showOutput)}",
        ]), ' COMMAND EXECUTION ORDER');
        $this->toSTDOUT(implode(PHP_EOL, [
            "   \$stack : {$stack}",
            "   \$useTeams : {$useTeamsStr}",
            "   \$showOutput : {$showOutputStr}",
            "   config(lambo.store.jetstream) : {$configStack}",
            "   config(lambo.store.show_output) : {$configShowOutputStr}",
        ]), ' TEST ITERATION CONTEXT');
    }
}
