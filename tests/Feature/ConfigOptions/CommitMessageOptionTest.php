<?php

namespace App\Feature;

use App\Facades\OptionManager;
use Tests\TestCase;

class CommitMessageOptionTest extends TestCase
{
    /** @test */
    function setting_an_initial_commit_message_at_runtime()
    {
        $this->artisan('new blog --commit="first commit"');

        $message = OptionManager::getOption('commit')->getOptionValue();

        $this->assertEquals('first commit', $message);
    }
}
