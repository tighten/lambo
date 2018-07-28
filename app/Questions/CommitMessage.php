<?php

namespace App\Questions;

use App\Support\BaseQuestion;
use LaravelZero\Framework\Commands\Command;

class CommitMessage extends BaseQuestion
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