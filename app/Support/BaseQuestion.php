<?php

namespace App\Support;


use App\Services\QuestionsService;
use Symfony\Component\Process\ExecutableFinder;

class BaseQuestion
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
     */
    public function answer($key, $value): void
    {
        $this->questionsService->answer($key, $value);
    }

}