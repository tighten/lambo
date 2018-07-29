<?php

namespace App\Actions;

use App\Support\BaseAction;
use App\Services\QuestionsService;

class AskConfigQuestions extends BaseAction
{
    /**
     * Ask the configuration questions.
     *
     * @return void
     */
    public function __invoke(): void
    {
        resolve(QuestionsService::class)->handle($this->console);
    }
}
