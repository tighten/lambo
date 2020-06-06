<?php

namespace Tests\Feature;

use App\Actions\OpenInEditor;
use App\LamboException;
use App\Shell;
use Illuminate\Support\Facades\Config;
use Tests\Feature\Fakes\FakeProcess;
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
            ->once()
            ->andReturn(FakeProcess::success());

        app(OpenInEditor::class)();
    }

    /** @test */
    function it_throws_an_exception_if_it_fails_to_open_the_editor()
    {
        $shell = $this->mock(Shell::class);

        Config::set('lambo.store.editor', 'my-editor');

        $editorCommand = "my-editor .";
        $shell->shouldReceive('execInProject')
            ->with($editorCommand)
            ->once()
            ->andReturn(FakeProcess::fail($editorCommand));

        $this->expectException(LamboException::class);

        app(OpenInEditor::class)();
    }
}
