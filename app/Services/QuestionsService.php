<?php

namespace App\Services;

use App\Questions\Editor;
use App\Questions\Release;
use App\Questions\CommitMessage;
use App\Support\QuestionAnswered;

class QuestionsService
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $answers;

    /**
     * @var array
     */
    public const questions = [
        Release::class,
        Editor::class,
        CommitMessage::class,
    ];

    /**
     * QuestionsService constructor.
     */
    public function __construct()
    {
        $this->answers = collect();
    }

    /**
     * Store an answer
     *
     * @param string $key
     * @param $value
     * @return QuestionAnswered
     */
    public function answer(string $key, $value): QuestionAnswered
    {
        $this->answers->put($key, $value);

        return new QuestionAnswered();
    }

    /**
     * Ask for all the options
     *
     * @param $command
     */
    public function handle($command): void
    {
        foreach (static::questions as $question) {
            app()->make($question)->handle($command);
        }
    }
}
