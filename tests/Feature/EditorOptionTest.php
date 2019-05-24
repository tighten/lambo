<?php

namespace App\Feature;

use App\Facades\OptionManager;
use App\Support\ShellCommand;
use Tests\TestCase;

class EditorOptionTest extends TestCase
{
    /** @test */
    function setting_the_editor_at_runtime()
    {
        $this->spy(ShellCommand::class);

        $this->artisan('new blog --editor=subl');

        $editor = OptionManager::getOption('editor')->getOptionValue();

        $this->assertEquals('subl', $editor);
    }
}
