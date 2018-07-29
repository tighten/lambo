<?php

namespace App\Actions;

use App\Support\BaseAction;
use App\Verifications\GitInstalled;
use App\Verifications\ValetInstalled;

class RunVerifications extends BaseAction
{
    /**
     * The verifications to be performed.
     *
     * @var array
     */
    protected $verifications = [
        ValetInstalled::class,
        GitInstalled::class,
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
                resolve($verification)->handle();
            } catch (\LogicException $exception) {
                $this->console->error($exception->getMessage());
                exit(1);
            } catch (\Exception $exception) {
                $this->console->error($exception->getMessage());
                exit(1);
            }
            if (!$verification) {
                $this->console->error("Verification {$verification} failed.");
                exit(1);
            }
        }
    }
}
