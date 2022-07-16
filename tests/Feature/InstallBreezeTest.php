<?php

namespace Tests\Feature;

use App\Actions\InstallBreeze;
use App\LamboException;
use App\Shell;
use Tests\TestCase;

/**
 * @group front-end-scaffolding
 */
class InstallBreezeTest extends TestCase
{
    /**
     * @test
     * @throws LamboException
     */
    function it_installs_laravel_breeze()
    {
        foreach ([false, true] as $withOutput) {
            foreach (InstallBreeze::VALID_STACKS as $stack) {
                config(['lambo.store.breeze' => $stack]);
                config(['lambo.store.with_output' => $withOutput]);

                if ($this->isVerbose()) {
                    $this->logUseCase($stack, $withOutput);
                }

                $this->shouldExecInProject($this->getComposerCommand($withOutput));
                $this->shouldExecInProject($this->getBreezeInstallCommand($stack, $withOutput));
                $this->shouldExecInProject($this->getNpmInstallCommand($withOutput));
                $this->shouldExecInProject($this->getCompileAssetsCommand($withOutput));

                app(InstallBreeze::class)();

                if ($this->isDebug()) {
                    $this->toSTDOUT("\n ✔ PASS\n");
                }
            }
        }
    }

    /**
     * @test
     * @throws LamboException
     */
    function it_skips_breeze_installation()
    {
        $this->spy(Shell::class);
        config(['lambo.store.breeze' => false]);

        app(InstallBreeze::class)();

        $this->shell->shouldNotHaveReceived('execInProject');
    }

    /** @test */
    function it_throws_a_lambo_exception_if_an_invalid_breeze_stack_is_requested()
    {
        config(['lambo.store.breeze' => null]);
        $this->expectException(LamboException::class);
        app(InstallBreeze::class)();

        config(['lambo.store.breeze' => '']);
        $this->expectException(LamboException::class);
        app(InstallBreeze::class)();

        config(['lambo.store.breeze' => 'invalid']);
        $this->expectException(LamboException::class);
        app(InstallBreeze::class)();

        config(['lambo.store.breeze' => true]);
        $this->expectException(LamboException::class);
        app(InstallBreeze::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_composer_installation_fails()
    {
        config(['lambo.store.breeze' => 'blade']);

        $this->shouldExecInProjectAndFail('composer require laravel/breeze --dev --quiet');
        $this->expectException(LamboException::class);

        app(InstallBreeze::class)();
    }

    /** @test */
    function it_throws_a_lambo_exception_if_breeze_installation_fails()
    {
        config(['lambo.store.breeze' => 'react']);

        $this->shouldExecInProject('composer require laravel/breeze --dev --quiet');
        $this->shouldExecInProjectAndFail('php artisan breeze:install react --quiet');

        $this->expectException(LamboException::class);

        app(InstallBreeze::class)();
    }

    private function getComposerCommand(bool $withOutput = false): string
    {
        return 'composer require laravel/breeze --dev' . ($withOutput ? '' : ' --quiet');
    }

    private function getBreezeInstallCommand(string $stack, $showOutput): string
    {
        return sprintf(
            'php artisan breeze:install%s%s',
            $stack === 'blade' ? '' : " {$stack}",
            $showOutput ? '' : ' --quiet'
        );
    }

    private function getNpmInstallCommand($showOutput): string
    {
        return 'npm install' . ($showOutput ? '' : ' --silent');
    }

    private function getCompileAssetsCommand($withOutput): string
    {
        return 'npm run build' . ($withOutput ? '' : ' --silent');
    }

    private function logUseCase(string $stack, $showOutput): void
    {
        $showOutputStr = $showOutput ? 'true' : 'false';

        $this->toSTDOUT("────────────────────────────\n");
        $this->toSTDOUT(implode(PHP_EOL, [
            sprintf('   lambo new <project> %s--breeze=%s', $showOutput ? '-v(vv) ' : '', $stack),
        ]), ' USE CASE');
        $this->toSTDOUT(implode(PHP_EOL, [
            "   1. {$this->getComposerCommand($showOutput)}",
            "   2. {$this->getBreezeInstallCommand($stack, $showOutput)}",
            "   3. {$this->getNpmInstallCommand($showOutput)}",
            "   4. {$this->getCompileAssetsCommand($showOutput)}",
        ]), ' COMMAND EXECUTION ORDER');
        $this->toSTDOUT(implode(PHP_EOL, [
            "   \$stack : {$stack}",
            "   \$showOutput : {$showOutputStr}",
            '   config(lambo.store.breeze) : ' . config('lambo.store.breeze'),
            '   config(lambo.store.show_output) : ' . (config('lambo.store.show_output') ? 'true' : 'false'),
        ]), ' TEST ITERATION CONTEXT');
    }
}
