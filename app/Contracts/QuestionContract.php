<?php

namespace App\Contracts;

use App\Support\QuestionAnswered;

interface QuestionContract
{
    /**
     * The Question must implement the answer method, by returning answer method
     * from \App\Services\QuestionsService::class
     *
     * @param string $key
     * @param $value
     * @return QuestionAnswered
     */
    public function answer(string $key, $value): QuestionAnswered;

}