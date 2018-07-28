<?php

namespace App\Questions;

use App\Support\BaseQuestion;
use LaravelZero\Framework\Commands\Command;

class Release extends BaseQuestion
{
    /**
     * @var string
     */
    protected $subject = 'dev';

    /**
     * Handle the question.
     *
     * @param $command
     */
    public function handle(Command $command): void
    {
        $releaseOrDevBranch = $command->choice('Release or Dev branch?', ['Release', 'Dev'], $default = 0);

        if ($releaseOrDevBranch === 'Release') {
            $this->answer($this->subject, false);
        } elseif ($releaseOrDevBranch === 'Dev') {
            $this->answer($this->subject, true);
        } else {
            $this->$command("Don't know what to do with such option.");
            exit(1);
        }
    }

}