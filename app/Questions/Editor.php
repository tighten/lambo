<?php

namespace App\Questions;

use App\Support\BaseQuestion;
use LaravelZero\Framework\Commands\Command;

class Editor extends BaseQuestion
{
    /**
     * @var string
     */
    protected $subject = 'editor';

    /**
     * Handle the question.
     *
     * @param $command
     */
    public function handle(Command $command): void
    {
        $options = collect([
            'PHPStorm'      => 'pstorm',
            'Sublime Text'  => 'subl',
            'Sublime'       => 'sublime',
            'Nonexisting'   => 'yadayada',
        ])->filter(function ($item, $key) {
            return $this->finder->find($item) !== null;
        });

        $editorKeyChoice = $command->choice('Open in editor?', $options->keys()->all(), $default = 0, $attempts = 3);

        $editor = $options->first(function($value, $key) use ($editorKeyChoice) {
                return $key === $editorKeyChoice;
            });

        $this->answer($this->subject, $editor);
    }

}