<?php

namespace App\ActionsPreInstall;

use Exception;
use LogicException;
use App\Support\BaseAction;
use App\Verifications\ExamplePasses;
use App\Verifications\GitInstalled;
use App\Verifications\ValetInstalled;
use App\Verifications\LaravelInstallerInstalled;

class RunVerifications extends BaseAction
{
    /**
     * The verifications to be performed.
     *
     * @var array
     */
    protected $verifications = [
        LaravelInstallerInstalled::class,
        GitInstalled::class,
        ValetInstalled::class,
        ExamplePasses::class,
    ];

    /**
     * Runs the verifications.
     *
     * @return void
     */
    public function __invoke(): void
    {
        foreach ($this->verifications as $verification) {
            try {
                $passes = resolve($verification)->handle();
            } catch (LogicException $exception) {
                $this->console->error("Verification {$verification} failed.");
                $this->console->error($exception->getMessage());
                exit(1);
            } catch (Exception $exception) {
                $this->console->error("Verification {$verification} failed.");
                $this->console->error($exception->getMessage());
                exit(1);
            }
            if (! $passes) {
                $this->console->error("Verification {$verification} failed.");
                exit(1);
            }
        }
    }
}
