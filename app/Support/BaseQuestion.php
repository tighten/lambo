<?php

namespace App\Support;

use App\Services\QuestionsService;
use App\Contracts\QuestionContract;
use Symfony\Component\Process\ExecutableFinder;

abstract class BaseQuestion implements QuestionContract
{
    /**
     * @var ExecutableFinder
     */
    protected $finder;

    /**
     * @var QuestionsService
     */
    protected $questionsService;

    public function __construct(ExecutableFinder $finder, QuestionsService $questionsService)
    {
        $this->finder = $finder;
        $this->questionsService = $questionsService;
    }

    /**
     * Store the answer in the singleton store
     *
     * @param $key
     * @param $value
     * @return QuestionAnswered
     */
    public function answer(string $key, $value): QuestionAnswered
    {
        return $this->questionsService->answer($key, $value);
    }

}