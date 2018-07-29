<?php

namespace App\InteractiveOptions;

use App\Support\BaseOption;
use LaravelZero\Framework\Commands\Command;

class CommitMessage extends BaseOption
{
    /**
     * @var string
     */
    protected $subject = 'commitMessage';

    /**
     * Handle the question.
     *
     * @param $command
     */
    public function handle(Command $command): void
    {
        $commitMessage= $command->ask('Commit message?', 'Initial commit.');

        $this->answer($this->subject, $commitMessage);
    }
}
