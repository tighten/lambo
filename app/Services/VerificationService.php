<?php

namespace App\Services;

use App\Verifications\GitInstalled;
use App\Verifications\ValetInstalled;
use LaravelZero\Framework\Commands\Command;

class VerificationService
{
    /**
     * Runs the verifications so that Lambo can run.
     *
     * @param Command $console
     */
    public function handle(Command $console): void
    {
        $verifications = [
            ValetInstalled::class,
            GitInstalled::class,
        ];

        foreach ($verifications as $verification) {
            try {
                resolve($verification)->handle();
            } catch (\LogicException $exception) {
                $console->error($exception->getMessage());
                exit(1);
            }
            catch (\Exception $exception) {
                $console->error($exception->getMessage());
                exit(1);
            }
            if (!$verification) {
                $console->error("Verification {$verification} failed.");
                exit(1);
            }
        }

//        $console->info('Verifications OK!');
    }
}
