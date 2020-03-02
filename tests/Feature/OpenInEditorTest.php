<?php

namespace Tests\Feature;

use App\Actions\OpenInEditor;
use App\Shell\Shell;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class OpenInEditorTest extends TestCase
{
    /** @test */
    function it_opens_the_project_folder_in_the_specified_editor()
    {
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.editor', 'my-editor');

        $shell->shouldReceive('execInProject')
            ->with("my-editor .")
            ->once();

        app(OpenInEditor::class)();
    }

    /** @test */
    function it_does_not_open_the_project_folder_if_an_editor_is_not_specified()
    {
        $shell = $this->spy(Shell::class);

        $this->assertEmpty(Config::get('lambo.store.editor'));

        app(OpenInEditor::class)();

        $shell->shouldNotHaveReceived('execInProject');
    }
}
