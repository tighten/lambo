<?php

namespace App\Actions;

use App\Support\BaseAction;
use App\Services\QuestionsService;

class AskConfigQuestions extends BaseAction
{
    public function __invoke()
    {
        resolve(QuestionsService::class)->handle($this->console);
    }
}