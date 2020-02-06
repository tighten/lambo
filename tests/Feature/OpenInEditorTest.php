<?php

namespace Tests\Feature;

use App\Actions\OpenInEditor;
use App\Shell\Shell;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class OpenInEditorTest extends TestCase
{
    /** @test */
    public function it_opens_the_project_folder_in_the_specified_editor()
    {
        $this->fakeLamboConsole();

        Config::set('lambo.store.editor', 'my-editor');

        $this->instance(Shell::class, Mockery::mock(Shell::class, function ($shell) {
            $shell->shouldReceive('execInProject')
                ->with("my-editor .")
                ->once();
        }));

        app(OpenInEditor::class)();
    }

    /** @test */
    public function it_does_not_open_the_project_folder_if_an_editor_is_not_specified()
    {
        $this->fakeLamboConsole();

        $this->assertEmpty(Config::get('lambo.store.editor'));

        $this->instance(Shell::class, Mockery::mock(Shell::class, function ($shell) {
            $shell->shouldNotReceive('execInProject')
                ->with("my-editor .");
        }));

        app(OpenInEditor::class)();
    }
}
